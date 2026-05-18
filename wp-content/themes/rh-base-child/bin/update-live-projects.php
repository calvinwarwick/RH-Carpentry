<?php
/**
 * One-off updates for rh_project posts on the live site (gallery trims, sector, trash).
 *
 * Run from WordPress root (SSH), after theme deploy:
 *
 *   wp eval-file wp-content/themes/rh-base-child/bin/update-live-projects.php
 *
 * Dry run (default): prints matches and planned changes, writes nothing.
 * Apply changes:
 *
 *   RH_PROJECT_UPDATES_APPLY=1 wp eval-file wp-content/themes/rh-base-child/bin/update-live-projects.php
 *
 * Requires WP-CLI. Projects must use Block editor galleries (core/gallery with attrs.ids
 * and/or core/image innerBlocks). If a job reports “no gallery attrs”, edit that post
 * in wp-admin once and re-run.
 *
 * @package RH_Base_Child
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
	exit(1);
}

$apply = getenv('RH_PROJECT_UPDATES_APPLY') === '1';

if (class_exists('WP_CLI', false)) {
	WP_CLI::log($apply ? 'MODE: APPLY (database will be updated)' : 'MODE: DRY RUN (no writes; set RH_PROJECT_UPDATES_APPLY=1 to apply)');
}

if (! post_type_exists('rh_project')) {
	if (class_exists('WP_CLI', false)) {
		WP_CLI::error('Post type rh_project is not registered.');
	}
	exit(1);
}

/**
 * @param string $title
 * @param string $needle
 */
function rh_update_live_projects_title_matches(string $title, string $needle): bool {
	$norm = static function (string $s): string {
		$s = strtolower($s);
		$s = preg_replace('/[^a-z0-9]+/i', ' ', $s);

		return trim(preg_replace('/\s+/', ' ', $s) ?? '');
	};

	return str_contains($norm($title), $norm($needle));
}

/**
 * @return list<int>
 */
function rh_update_live_projects_find_ids(string $needle, ?string $exclude_needle = null): array {
	$hits = array();
	$q    = new WP_Query(
		array(
			'post_type'              => 'rh_project',
			'post_status'            => array('publish', 'draft', 'pending', 'private'),
			'posts_per_page'         => -1,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);
	foreach ($q->posts as $pid) {
		$pid = (int) $pid;
		$t   = get_the_title($pid);
		if (! is_string($t) || ! rh_update_live_projects_title_matches($t, $needle)) {
			continue;
		}
		if ($exclude_needle !== null && $exclude_needle !== '' && rh_update_live_projects_title_matches($t, $exclude_needle)) {
			continue;
		}
		$hits[] = $pid;
	}

	return $hits;
}

/**
 * @param list<int> $hits
 */
function rh_update_live_projects_require_unique(string $needle, array $hits): ?int {
	if ($hits === array()) {
		if (class_exists('WP_CLI', false)) {
			WP_CLI::warning("No project matched needle: {$needle}");
		}

		return null;
	}
	if (count($hits) > 1) {
		$titles = array();
		foreach ($hits as $id) {
			$titles[] = '#' . $id . ' ' . get_the_title($id);
		}
		if (class_exists('WP_CLI', false)) {
			WP_CLI::warning("Multiple projects matched \"{$needle}\": " . implode(' | ', $titles) . ' — skipping.');
		}

		return null;
	}

	return $hits[0];
}

/**
 * @return array{0: int, 1: int} [image_count, gallery_block_count]
 */
function rh_update_live_projects_count_gallery_images(string $content): array {
	$blocks = parse_blocks($content);
	$counts = rh_update_live_projects_walk_count_galleries($blocks);

	return array($counts['images'], $counts['galleries']);
}

/**
 * @param list<array<string, mixed>> $blocks
 * @return array{images: int, galleries: int}
 */
function rh_update_live_projects_walk_count_galleries(array $blocks): array {
	$images   = 0;
	$galleries = 0;
	foreach ($blocks as $block) {
		if (($block['blockName'] ?? '') === 'core/gallery') {
			++$galleries;
			if (! empty($block['attrs']['ids']) && is_array($block['attrs']['ids'])) {
				$images += count($block['attrs']['ids']);
			} elseif (! empty($block['innerBlocks']) && is_array($block['innerBlocks'])) {
				foreach ($block['innerBlocks'] as $ch) {
					if (($ch['blockName'] ?? '') === 'core/image') {
						++$images;
					}
				}
			}
		}
		if (! empty($block['innerBlocks']) && is_array($block['innerBlocks'])) {
			$sub = rh_update_live_projects_walk_count_galleries($block['innerBlocks']);
			$images += $sub['images'];
			$galleries += $sub['galleries'];
		}
	}

	return array('images' => $images, 'galleries' => $galleries);
}

/**
 * @param array<string, mixed> $block
 * @return array<string, mixed>|null
 */
function rh_update_live_projects_trim_gallery_block(array $block, int $drop_first, int $drop_last): ?array {
	if (($block['blockName'] ?? '') !== 'core/gallery') {
		return $block;
	}

	$df = max(0, $drop_first);
	$dl = max(0, $drop_last);

	$had_ids = ! empty($block['attrs']['ids']) && is_array($block['attrs']['ids']);
	if ($had_ids) {
		$ids = array_values(array_map('intval', $block['attrs']['ids']));
		$n   = count($ids);
		if ($n > 0) {
			if ($df + $dl >= $n) {
				$block['attrs']['ids'] = array();
			} else {
				$block['attrs']['ids'] = array_values(array_slice($ids, $df, $n - $df - $dl));
			}
		}
		$block['innerHTML']     = '';
		$block['innerContent'] = null;
	}

	if (! empty($block['innerBlocks']) && is_array($block['innerBlocks'])) {
		$ib           = $block['innerBlocks'];
		$img_positions = array();
		foreach ($ib as $i => $child) {
			if (($child['blockName'] ?? '') === 'core/image') {
				$img_positions[] = $i;
			}
		}
		$total = count($img_positions);
		if ($total > 0) {
			$keep_count = $total - $df - $dl;
			if ($keep_count <= 0) {
				$new_ib = array();
				foreach ($ib as $child) {
					if (($child['blockName'] ?? '') !== 'core/image') {
						$new_ib[] = $child;
					}
				}
				$block['innerBlocks'] = $new_ib;
			} else {
				$drop_idx = array();
				for ($i = 0; $i < $df; $i++) {
					$drop_idx[$img_positions[$i]] = true;
				}
				for ($i = $total - $dl; $i < $total; $i++) {
					$drop_idx[$img_positions[$i]] = true;
				}
				$new_ib = array();
				foreach ($ib as $idx => $child) {
					if (($child['blockName'] ?? '') === 'core/image' && isset($drop_idx[$idx])) {
						continue;
					}
					$new_ib[] = $child;
				}
				$block['innerBlocks'] = $new_ib;
			}
		}
		$block['innerHTML']     = '';
		$block['innerContent'] = null;
	}

	$ids_count = isset($block['attrs']['ids']) && is_array($block['attrs']['ids']) ? count($block['attrs']['ids']) : 0;
	$inner_imgs = 0;
	foreach ($block['innerBlocks'] ?? array() as $ch) {
		if (($ch['blockName'] ?? '') === 'core/image') {
			++$inner_imgs;
		}
	}
	if ($ids_count === 0 && $inner_imgs === 0) {
		return null;
	}

	return $block;
}

/**
 * @param list<array<string, mixed>> $blocks
 * @return list<array<string, mixed>>
 */
function rh_update_live_projects_map_blocks(array $blocks, int $drop_first, int $drop_last): array {
	$out = array();
	foreach ($blocks as $block) {
		if (($block['blockName'] ?? '') === 'core/gallery') {
			$trimmed = rh_update_live_projects_trim_gallery_block($block, $drop_first, $drop_last);
			if ($trimmed === null) {
				continue;
			}
			$block = $trimmed;
		}
		if (! empty($block['innerBlocks']) && is_array($block['innerBlocks'])) {
			$block['innerBlocks'] = rh_update_live_projects_map_blocks($block['innerBlocks'], $drop_first, $drop_last);
		}
		$out[] = $block;
	}

	return $out;
}

/**
 * Trims every core/gallery in post_content the same way.
 */
function rh_update_live_projects_trim_post_galleries(int $post_id, int $drop_first, int $drop_last, bool $apply): bool {
	$post = get_post($post_id);
	if (! $post instanceof WP_Post) {
		return false;
	}
	$content = (string) $post->post_content;
	if ($content === '') {
		if (class_exists('WP_CLI', false)) {
			WP_CLI::warning("Post #{$post_id} has empty content.");
		}

		return false;
	}
	$before = rh_update_live_projects_count_gallery_images($content);
	$blocks = parse_blocks($content);
	$blocks = rh_update_live_projects_map_blocks($blocks, $drop_first, $drop_last);
	$new    = serialize_blocks($blocks);
	$after  = rh_update_live_projects_count_gallery_images($new);

	if (class_exists('WP_CLI', false)) {
		WP_CLI::log("  Gallery images: {$before[0]} across {$before[1]} block(s) → {$after[0]} across {$after[1]} block(s)");
	}

	if ($before[0] === 0 && class_exists('WP_CLI', false)) {
		WP_CLI::warning('  No gallery images detected in block markup (ids / core:image). Edit in wp-admin or convert to block gallery.');
	}

	if ($new === $content) {
		if (class_exists('WP_CLI', false)) {
			WP_CLI::log('  No HTML change (already trimmed or unsupported gallery format).');
		}

		return false;
	}

	if ($apply) {
		wp_update_post(
			array(
				'ID'           => $post_id,
				'post_content' => $new,
			)
		);
	}

	return true;
}

/**
 * @return array<string, mixed>
 */
function rh_update_live_jobs(): array {
	return array(
		array(
			'label'   => 'Willow farm → sector New builds',
			'needle'  => 'Willow farm',
			'action'  => 'taxonomy_new_builds',
		),
		array(
			'label'   => 'Collingwood Road — remove last 2 gallery images',
			'needle'  => 'Collingwood Road',
			'action'  => 'gallery',
			'drop_f'  => 0,
			'drop_l'  => 2,
		),
		array(
			'label'   => 'Bromley road — trash project',
			'needle'  => 'Bromley road',
			'action'  => 'trash',
		),
		array(
			'label'   => 'Garage Cart Lodge — remove bottom 3 photos',
			'needle'  => 'Garage Cart Lodge',
			'action'  => 'gallery',
			'drop_f'  => 0,
			'drop_l'  => 3,
		),
		array(
			'label'   => 'Muckmurdos — remove bottom 3 photos',
			'needle'  => 'Muckmurdos',
			'action'  => 'gallery',
			'drop_f'  => 0,
			'drop_l'  => 3,
		),
		array(
			'label'   => 'Kirby — remove bottom 3 photos',
			'needle'  => 'Kirby',
			'exclude' => 'le-Soken',
			'action'  => 'gallery',
			'drop_f'  => 0,
			'drop_l'  => 3,
		),
		array(
			'label'   => 'Lt.Clacton — remove bottom 3 photos',
			'needle'  => 'Lt Clacton',
			'action'  => 'gallery',
			'drop_f'  => 0,
			'drop_l'  => 3,
		),
		array(
			'label'   => 'Birch House — remove bottom 3 photos',
			'needle'  => 'Birch House',
			'action'  => 'gallery',
			'drop_f'  => 0,
			'drop_l'  => 3,
		),
		array(
			'label'   => 'John Warner School — remove bottom 3 photos',
			'needle'  => 'John Warner',
			'action'  => 'gallery',
			'drop_f'  => 0,
			'drop_l'  => 3,
		),
		array(
			'label'   => '7 Dwellings — remove bottom 3 photos',
			'needle'  => '7 Dwellings',
			'action'  => 'gallery',
			'drop_f'  => 0,
			'drop_l'  => 3,
		),
		array(
			'label'   => 'Papworth Village Hall — remove bottom 3 photos',
			'needle'  => 'Papworth Village Hall',
			'action'  => 'gallery',
			'drop_f'  => 0,
			'drop_l'  => 3,
		),
		array(
			'label'   => 'Victoria Espange — remove top 2 and bottom 3 photos',
			'needle'  => 'Victoria Espange',
			'action'  => 'gallery',
			'drop_f'  => 2,
			'drop_l'  => 3,
		),
		array(
			'label'   => 'Barton Road — remove bottom 2 photos',
			'needle'  => 'Barton Road',
			'action'  => 'gallery',
			'drop_f'  => 0,
			'drop_l'  => 2,
		),
		array(
			'label'   => 'Allen House — remove bottom 3 photos',
			'needle'  => 'Allen House',
			'action'  => 'gallery',
			'drop_f'  => 0,
			'drop_l'  => 3,
		),
		array(
			'label'   => 'Willow cottage — remove bottom 3 photos',
			'needle'  => 'Willow cottage',
			'action'  => 'gallery',
			'drop_f'  => 0,
			'drop_l'  => 3,
		),
		array(
			'label'   => 'Kirby-le-Soken — remove bottom 3 photos',
			'needle'  => 'Kirby-le-Soken',
			'action'  => 'gallery',
			'drop_f'  => 0,
			'drop_l'  => 3,
		),
		array(
			'label'   => 'Flat 3 — remove bottom 3 photos',
			'needle'  => 'Flat 3',
			'action'  => 'gallery',
			'drop_f'  => 0,
			'drop_l'  => 3,
		),
	);
}

foreach (rh_update_live_jobs() as $job) {
	$needle = (string) $job['needle'];
	$label  = (string) $job['label'];
	if (class_exists('WP_CLI', false)) {
		WP_CLI::log('');
		WP_CLI::log("— {$label}");
	}

	$exclude = isset($job['exclude']) && $job['exclude'] !== '' ? (string) $job['exclude'] : null;
	$hits    = rh_update_live_projects_find_ids($needle, $exclude);
	$id   = rh_update_live_projects_require_unique($needle, $hits);
	if ($id === null) {
		continue;
	}

	if (class_exists('WP_CLI', false)) {
		WP_CLI::log('  Post #' . $id . ' ' . get_the_title($id));
	}

	$action = (string) $job['action'];
	if ($action === 'taxonomy_new_builds') {
		$term = get_term_by('name', 'New builds', 'rh_project_sector');
		if (! $term instanceof WP_Term) {
			$term = get_term_by('slug', 'new-builds', 'rh_project_sector');
		}
		if (! $term instanceof WP_Term) {
			if (class_exists('WP_CLI', false)) {
				WP_CLI::warning('  Term "New builds" not found in rh_project_sector. Create it in wp-admin, then re-run.');
			}
			continue;
		}
		if (class_exists('WP_CLI', false)) {
			WP_CLI::log('  Set sector to New builds (term_id ' . (int) $term->term_id . ')');
		}
		if ($apply) {
			wp_set_object_terms($id, array((int) $term->term_id), 'rh_project_sector', false);
		}
		continue;
	}

	if ($action === 'trash') {
		if (class_exists('WP_CLI', false)) {
			WP_CLI::log('  Trash post');
		}
		if ($apply) {
			wp_trash_post($id);
		}
		continue;
	}

	if ($action === 'gallery') {
		$df = (int) $job['drop_f'];
		$dl = (int) $job['drop_l'];
		rh_update_live_projects_trim_post_galleries($id, $df, $dl, $apply);
		continue;
	}
}

if (class_exists('WP_CLI', false)) {
	WP_CLI::success($apply ? 'Updates applied.' : 'Dry run complete. Re-run with RH_PROJECT_UPDATES_APPLY=1 to write changes.');
}
