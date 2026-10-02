<?php
/**
 * generate_todo_view.php
 *
 * Génère une vue groupée (Type > Priorité) à partir de doc/todo.md
 * (format "nœud par tâche"), sans jamais modifier le fichier source.
 *
 * Navigateur : affiche une page HTML lisible et stylée.
 *   http://localhost/rilsport/doc/generate_todo_view.php
 *   http://localhost/rilsport/doc/generate_todo_view.php?status=Nouveau,En cours
 *   http://localhost/rilsport/doc/generate_todo_view.php?type=Bug&priority=P1
 *
 * Ligne de commande : affiche (ou écrit) du Markdown propre.
 *   php doc/generate_todo_view.php
 *   php doc/generate_todo_view.php --output=doc/todo_view.md
 *   php doc/generate_todo_view.php --status=Nouveau,"En cours"
 */

$isCli = (php_sapi_name() === 'cli');

if ($isCli) {
    $options = getopt('', ['output::', 'status::', 'type::', 'priority::']);
} else {
    $options = array_filter([
        'output' => $_GET['output'] ?? null,
        'status' => $_GET['status'] ?? null,
        'type' => $_GET['type'] ?? null,
        'priority' => $_GET['priority'] ?? null,
    ], fn($v) => $v !== null && $v !== '');
}

$sourceFile = __DIR__ . '/todo.md';

if (!file_exists($sourceFile)) {
    $msg = "Fichier introuvable : doc/todo.md";
    if ($isCli) {
        fwrite(STDERR, $msg . "\n");
        exit(1);
    }
    header('Content-Type: text/plain; charset=utf-8');
    echo $msg;
    exit(1);
}

// Normalisation des fins de ligne (le fichier peut avoir été ré-enregistré sous Windows).
$content = file_get_contents($sourceFile);
$content = str_replace(["\r\n", "\r"], "\n", $content);

// Repérage de chaque tâche par son titre "### `[ID]` Titre", jusqu'au titre suivant.
preg_match_all('/^###\s+`\[(\d{8}-\d{4})\]`\s*(.+?)\s*$/m', $content, $matches, PREG_OFFSET_CAPTURE);

$tasks = [];
$count = count($matches[0]);
for ($i = 0; $i < $count; $i++) {
    $id = $matches[1][$i][0];
    $title = trim($matches[2][$i][0]);
    $startPos = $matches[0][$i][1] + strlen($matches[0][$i][0]);
    $endPos = ($i + 1 < $count) ? $matches[0][$i + 1][1] : strlen($content);
    $block = substr($content, $startPos, $endPos - $startPos);

    preg_match('/\*\*Type\*\*\s*:\s*(.+)/', $block, $mType);
    preg_match('/\*\*Priorité\*\*\s*:\s*(.+)/', $block, $mPrio);
    preg_match('/\*\*Statut\*\*\s*:\s*(.+)/', $block, $mStatus);
    preg_match('/\*\*Description\*\*\s*:\s*(.+)/', $block, $mDesc);

    // Sous-tâches : tout ce qui suit le marqueur "**Sous-tâches** :" dans le bloc.
    $subtasks = [];
    if (preg_match('/\*\*Sous-tâches\*\*\s*:\s*\n(.*)/s', $block, $mSub)) {
        preg_match_all(
            '/^(\s*)- (\[[ x\-]?\])\s*(?:`\[(\d{8}-\d{4})\]`)?\s*(.*)$/m',
            $mSub[1],
            $subMatches,
            PREG_SET_ORDER
        );
        foreach ($subMatches as $sm) {
            $subtasks[] = [
                'level' => (int) floor(strlen($sm[1]) / 4),
                'status' => $sm[2],
                'id' => $sm[3] ?? '',
                'text' => trim($sm[4]),
            ];
        }
    }

    $tasks[] = [
        'id' => $id,
        'title' => $title,
        'type' => trim($mType[1] ?? ''),
        'priority' => trim($mPrio[1] ?? ''),
        'status' => trim($mStatus[1] ?? ''),
        'desc' => trim($mDesc[1] ?? ''),
        'subtasks' => $subtasks,
    ];
}

foreach (['status', 'type', 'priority'] as $filterKey) {
    if (!empty($options[$filterKey])) {
        $wanted = array_map('trim', explode(',', $options[$filterKey]));
        $tasks = array_values(array_filter($tasks, fn($t) => in_array($t[$filterKey], $wanted, true)));
    }
}

$grouped = [];
foreach ($tasks as $t) {
    $type = $t['type'] !== '' ? $t['type'] : '(Type non défini)';
    $prio = $t['priority'] !== '' ? $t['priority'] : '(Priorité non définie)';
    $grouped[$type][$prio][] = $t;
}
$prioOrder = ['P1' => 1, 'P2' => 2, 'P3' => 3, 'P4' => 4];
$typeOrder = ['Bug' => 1, 'Évolution' => 2, 'Question / Idées' => 3];
uksort($grouped, fn($a, $b) => ($typeOrder[$a] ?? 99) <=> ($typeOrder[$b] ?? 99));
foreach ($grouped as &$byPrio) {
    uksort($byPrio, fn($a, $b) => ($prioOrder[$a] ?? 99) <=> ($prioOrder[$b] ?? 99));
}
unset($byPrio);

function filterUrl(array $overrides): string
{
    $params = $_GET;
    foreach ($overrides as $k => $v) {
        if ($v === null) {
            unset($params[$k]);
        } else {
            $params[$k] = $v;
        }
    }
    unset($params['output']);
    $qs = http_build_query($params);
    return $qs === '' ? '?' : '?' . $qs;
}

function statusIcon(string $status): string
{
    return match ($status) { 'Réalisé' => '[x]', 'En cours' => '[-]', default => '[ ]'};
}

/**
 * Convertit une notation "[x]" / "[-]" / "[ ]" / "[]" en icône visuelle colorée
 * (coche verte / orange / vide) pour l'affichage HTML.
 */
function statusIconHtml(string $raw): string
{
    $raw = trim($raw);
    return match ($raw) {
        '[x]' => '<span class="ico ico-done" title="Réalisé">✔</span>',
        '[-]' => '<span class="ico ico-progress" title="En cours">●</span>',
        default => '<span class="ico ico-todo" title="À faire">○</span>',
    };
}

/**
 * Échappe tout le HTML SAUF une petite liste blanche de balises de mise en
 * forme déjà utilisées dans todo.md (strong, u, em...), et retire tout
 * attribut éventuel sur ces balises par précaution.
 */
function safeHtml(string $text): string
{
    $allowed = '<strong><b><em><i><u><code>';
    $text = strip_tags($text, $allowed);
    $text = preg_replace('/<(strong|b|em|i|u|code)\b[^>]*>/i', '<$1>', $text);
    return $text;
}

// ---------- Sortie Markdown (CLI, ou écriture fichier --output) ----------
function buildMarkdown(array $grouped, int $total): string
{
    $out = [];
    $out[] = "# Vue groupée — TODO (générée automatiquement, ne pas éditer)";
    $out[] = "> Générée le " . date('Y-m-d H:i') . " à partir de `doc/todo.md`. Toute modification doit se faire dans `doc/todo.md`, pas ici.";
    $out[] = "> {$total} tâche(s) au total.";
    $out[] = "";
    foreach ($grouped as $type => $byPrio) {
        $out[] = "## {$type}";
        foreach ($byPrio as $prio => $items) {
            $out[] = "### {$prio}";
            foreach ($items as $t) {
                $out[] = "- " . statusIcon($t['status']) . " `[{$t['id']}]` **{$t['title']}**" . ($t['status'] ? " _( {$t['status']} )_" : '');
            }
            $out[] = "";
        }
    }
    return implode("\n", $out) . "\n";
}

if (!empty($options['output'])) {
    $outPath = $options['output'];
    if (!preg_match('#^([A-Za-z]:\\\\|/)#', $outPath)) {
        $outPath = __DIR__ . '/../' . ltrim($outPath, '/');
    }
    file_put_contents($outPath, buildMarkdown($grouped, count($tasks)));
    echo "Vue générée : {$options['output']} (" . count($tasks) . " tâche(s))\n";
    exit;
}

if ($isCli) {
    echo buildMarkdown($grouped, count($tasks));
    exit;
}

// ---------- Sortie HTML (navigateur) ----------
header('Content-Type: text/html; charset=utf-8');

$statusColor = ['Nouveau' => '#0072c6', 'En cours' => '#e3a21a', 'Réalisé' => '#4caf50'];
$typeColor = ['Bug' => '#d9534f', 'Évolution' => '#0072c6', 'Question / Idées' => '#8e44ad'];

?><!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>TODO rilsport</title>
    <style>
        body {
            font-family: Segoe UI, Arial, sans-serif;
            max-width: 900px;
            margin: 30px auto;
            padding: 0 20px;
            color: #222;
            background: #f7f7f8;
        }

        h1 {
            font-size: 22px;
            margin-bottom: 4px;
        }

        .meta {
            color: #777;
            font-size: 13px;
            margin-bottom: 24px;
        }

        h2 {
            margin-top: 34px;
            padding: 8px 14px;
            border-radius: 6px;
            color: #fff;
            font-size: 17px;
        }

        .type-section {
            margin-top: 20px;
        }

        .type-summary {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border-radius: 6px;
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            list-style: none;
        }

        .type-summary::-webkit-details-marker {
            display: none;
        }

        .type-summary::before {
            content: '▾';
            font-size: 13px;
            transition: transform .15s;
        }

        .type-section:not([open]) .type-summary::before {
            content: '▸';
        }

        .type-count {
            font-weight: 400;
            opacity: .85;
            font-size: 14px;
        }

        .type-body {
            padding-top: 4px;
        }

        .prio-section {
            margin-top: 18px;
        }

        .prio-summary {
            font-size: 14px;
            text-transform: uppercase;
            color: #555;
            letter-spacing: .5px;
            font-weight: 600;
            cursor: pointer;
            list-style: none;
            padding: 4px 0;
        }

        .prio-summary::-webkit-details-marker {
            display: none;
        }

        .prio-summary::before {
            content: '▾ ';
            font-size: 11px;
            color: #999;
        }

        .prio-section:not([open]) .prio-summary::before {
            content: '▸ ';
        }

        .prio-count {
            color: #bbb;
            font-weight: 400;
            text-transform: none;
        }

        ul {
            list-style: none;
            padding-left: 0;
            margin: 0 0 10px 0;
        }

        li {
            background: #fff;
            border: 1px solid #e3e3e3;
            border-radius: 6px;
            margin-bottom: 6px;
            overflow: hidden;
        }

        details {}

        summary {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            cursor: pointer;
            list-style: none;
        }

        summary::-webkit-details-marker {
            display: none;
        }

        summary::before {
            content: '▸';
            color: #999;
            font-size: 11px;
            width: 10px;
            flex-shrink: 0;
            transition: transform .15s;
        }

        details[open] summary::before {
            transform: rotate(90deg);
        }

        details[open] summary {
            border-bottom: 1px solid #eee;
            background: #fafafa;
        }

        .id {
            font-family: Consolas, monospace;
            font-size: 11px;
            color: #999;
            white-space: nowrap;
        }

        .title {
            flex: 1;
            font-size: 14px;
        }

        .badge {
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 10px;
            color: #fff;
            white-space: nowrap;
        }

        .check {
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }

        .ico {
            display: inline-block;
            font-size: 15px;
            font-weight: bold;
        }

        .ico-done {
            color: #2e7d32;
        }

        .ico-progress {
            color: #e3a21a;
        }

        .ico-todo {
            color: #ccc;
        }

        .empty {
            color: #999;
            font-style: italic;
            padding: 8px 0;
        }

        .filters {
            font-size: 13px;
            color: #666;
            margin-bottom: 10px;
        }

        .filters a {
            color: #0072c6;
        }

        .detail {
            padding: 10px 14px 12px 32px;
            font-size: 13px;
        }

        .detail .desc {
            color: #444;
            margin-bottom: 8px;
            line-height: 1.5;
        }

        .detail .no-desc {
            color: #aaa;
            font-style: italic;
            margin-bottom: 8px;
        }

        .sub-list {
            margin: 0;
            padding: 0;
        }

        .sub-list div {
            display: flex;
            align-items: baseline;
            gap: 8px;
            padding: 3px 0;
            color: #333;
        }

        .sub-list .sub-check {
            width: 16px;
            flex-shrink: 0;
        }

        .sub-list .sub-id {
            font-family: Consolas, monospace;
            font-size: 10px;
            color: #bbb;
            flex-shrink: 0;
        }

        .no-sub {
            color: #aaa;
            font-style: italic;
        }

        .filter-section {
            background: #fff;
            border: 1px solid #e3e3e3;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .filter-summary {
            padding: 10px 14px;
            font-size: 13px;
            font-weight: 600;
            color: #444;
            cursor: pointer;
            list-style: none;
        }

        .filter-summary::-webkit-details-marker {
            display: none;
        }

        .filter-summary::before {
            content: '▾ ';
            font-size: 11px;
            color: #999;
        }

        .filter-section:not([open]) .filter-summary::before {
            content: '▸ ';
        }

        .filter-section[open] .filter-summary {
            border-bottom: 1px solid #eee;
        }

        .filter-active-count {
            font-weight: 400;
            color: #0072c6;
        }

        .filter-bar {
            padding: 12px 14px;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 8px;
        }

        .filter-group:last-child {
            margin-bottom: 0;
        }

        .filter-label {
            font-size: 12px;
            color: #888;
            width: 70px;
            flex-shrink: 0;
            text-transform: uppercase;
            letter-spacing: .3px;
        }

        .pill {
            font-size: 12px;
            padding: 4px 11px;
            border-radius: 12px;
            text-decoration: none;
            border: 1px solid #ddd;
            color: #555;
            background: #fafafa;
            white-space: nowrap;
        }

        .pill:hover {
            border-color: #999;
        }

        .pill.active {
            color: #fff;
            border-color: transparent;
        }

        .pill.c-status-Nouveau {
            color: #0072c6;
            border-color: #b9dcf2;
            background: #eef7fd;
        }

        .pill.c-status-Nouveau.active {
            background: #0072c6;
            color: #fff;
        }

        .pill.c-status-Encours {
            color: #b9791a;
            border-color: #f2ddb0;
            background: #fdf6e8;
        }

        .pill.c-status-Encours.active {
            background: #e3a21a;
            color: #fff;
        }

        .pill.c-status-Réalisé {
            color: #2e7d32;
            border-color: #bfe3c1;
            background: #eef8ef;
        }

        .pill.c-status-Réalisé.active {
            background: #4caf50;
            color: #fff;
        }

        .pill.c-prio {
            color: #555;
        }

        .pill.active.c-prio {
            background: #555;
            color: #fff;
        }

        .pill.c-prio-P1 {
            color: #c0392b;
            border-color: #f0c4bf;
            background: #fdf1f0;
        }

        .pill.c-prio-P1.active {
            background: #e74c3c;
            color: #fff;
        }

        .pill.c-prio-P2 {
            color: #c46a12;
            border-color: #f2d7b9;
            background: #fdf3ea;
        }

        .pill.c-prio-P2.active {
            background: #e67e22;
            color: #fff;
        }

        .pill.c-prio-P3 {
            color: #ab8a0c;
            border-color: #f0e3ae;
            background: #fdf9ea;
        }

        .pill.c-prio-P3.active {
            background: #d4a017;
            color: #fff;
        }

        .pill.c-prio-P4 {
            color: #6c7a7a;
            border-color: #d8dede;
            background: #f4f6f6;
        }

        .pill.c-prio-P4.active {
            background: #7f8c8d;
            color: #fff;
        }

        .pill.c-type-Bug {
            color: #c0392b;
            border-color: #f0c4bf;
            background: #fdf1f0;
        }

        .pill.c-type-Bug.active {
            background: #d9534f;
            color: #fff;
        }

        .pill.c-type-Évolution {
            color: #0072c6;
            border-color: #b9dcf2;
            background: #eef7fd;
        }

        .pill.c-type-Évolution.active {
            background: #0072c6;
            color: #fff;
        }

        .pill.c-type-QuestionIdées {
            color: #8e44ad;
            border-color: #ddc2e8;
            background: #f8f0fa;
        }

        .pill.c-type-QuestionIdées.active {
            background: #8e44ad;
            color: #fff;
        }

        .pill.c-tout.active {
            background: #333;
            color: #fff;
        }
    </style>
</head>

<body>
    <h1>📋 TODO rilsport</h1>
    <div class="meta">Générée le <?= date('d/m/Y à H:i') ?> à partir de <code>doc/todo.md</code> — <?= count($tasks) ?>
        tâche(s). Cette page est <strong>en lecture seule</strong> : toute modification se fait dans
        <code>doc/todo.md</code>.</div>
    <?php
    $curStatus = $_GET['status'] ?? '';
    $curType = $_GET['type'] ?? '';
    $curPrio = $_GET['priority'] ?? '';
    $statusClassMap = ['Nouveau' => 'Nouveau', 'En cours' => 'Encours', 'Réalisé' => 'Réalisé'];
    $typeClassMap = ['Bug' => 'Bug', 'Évolution' => 'Évolution', 'Question / Idées' => 'QuestionIdées'];
    ?>
    <?php $activeFilterCount = ($curType !== '' ? 1 : 0) + ($curPrio !== '' ? 1 : 0) + ($curStatus !== '' ? 1 : 0); ?>
    <details class="filter-section" open>
        <summary class="filter-summary">
            Filtres<?= $activeFilterCount > 0 ? ' <span class="filter-active-count">(' . $activeFilterCount . ' actif' . ($activeFilterCount > 1 ? 's' : '') . ')</span>' : '' ?>
        </summary>
        <div class="filter-bar">
            <div class="filter-group">
                <span class="filter-label">Type</span>
                <a class="pill <?= $curType === '' ? 'active c-tout' : '' ?>"
                    href="<?= filterUrl(['type' => null]) ?>">Tout</a>
                <a class="pill <?= $curType === 'Bug' ? 'active' : '' ?> c-type-Bug"
                    href="<?= filterUrl(['type' => 'Bug']) ?>">Bugs</a>
                <a class="pill <?= $curType === 'Évolution' ? 'active' : '' ?> c-type-Évolution"
                    href="<?= filterUrl(['type' => 'Évolution']) ?>">Évolutions</a>
                <a class="pill <?= $curType === 'Question / Idées' ? 'active' : '' ?> c-type-QuestionIdées"
                    href="<?= filterUrl(['type' => 'Question / Idées']) ?>">Questions / Idées</a>
            </div>
            <div class="filter-group">
                <span class="filter-label">Priorité</span>
                <a class="pill <?= $curPrio === '' ? 'active c-tout' : '' ?>"
                    href="<?= filterUrl(['priority' => null]) ?>">Tout</a>
                <?php foreach (['P1', 'P2', 'P3', 'P4'] as $p): ?>
                    <a class="pill <?= $curPrio === $p ? 'active' : '' ?> c-prio-<?= $p ?>"
                        href="<?= filterUrl(['priority' => $p]) ?>"><?= $p ?></a>
                <?php endforeach; ?>
            </div>
            <div class="filter-group">
                <span class="filter-label">Statut</span>
                <a class="pill <?= $curStatus === '' ? 'active c-tout' : '' ?>"
                    href="<?= filterUrl(['status' => null]) ?>">Tout</a>
                <a class="pill <?= $curStatus === 'Nouveau' ? 'active' : '' ?> c-status-Nouveau"
                    href="<?= filterUrl(['status' => 'Nouveau']) ?>">○ Nouveau</a>
                <a class="pill <?= $curStatus === 'En cours' ? 'active' : '' ?> c-status-Encours"
                    href="<?= filterUrl(['status' => 'En cours']) ?>">● En cours</a>
                <a class="pill <?= $curStatus === 'Réalisé' ? 'active' : '' ?> c-status-Réalisé"
                    href="<?= filterUrl(['status' => 'Réalisé']) ?>">✔ Réalisé</a>
            </div>
        </div>
    </details>

    <?php if (empty($grouped)): ?>
        <p class="empty">Aucune tâche ne correspond aux filtres actuels.</p>
    <?php endif; ?>

    <?php
    $typePlural = ['Bug' => 'Bugs', 'Évolution' => 'Évolutions', 'Question / Idées' => 'Questions / Idées'];
    ?>
    <?php foreach ($grouped as $type => $byPrio): ?>
        <?php $typeTotal = array_sum(array_map('count', $byPrio)); ?>
        <details class="type-section" open>
            <summary class="type-summary" style="background: <?= $typeColor[$type] ?? '#666' ?>;">
                <span class="type-name"><?= htmlspecialchars($typePlural[$type] ?? $type) ?></span>
                <span class="type-count">(<?= $typeTotal ?>)</span>
            </summary>
            <div class="type-body">
                <?php foreach ($byPrio as $prio => $items): ?>
                    <details class="prio-section" open>
                        <summary class="prio-summary"><?= htmlspecialchars($prio) ?> <span
                                class="prio-count">(<?= count($items) ?>)</span></summary>
                        <ul>
                            <?php foreach ($items as $t): ?>
                                <li>
                                    <details>
                                        <summary>
                                            <span class="check"><?= statusIconHtml(statusIcon($t['status'])) ?></span>
                                            <span class="id">[<?= htmlspecialchars($t['id']) ?>]</span>
                                            <span class="title"><?= safeHtml($t['title']) ?></span>
                                            <?php if ($t['status']): ?>
                                                <span class="badge"
                                                    style="background: <?= $statusColor[$t['status']] ?? '#999' ?>;"><?= htmlspecialchars($t['status']) ?></span>
                                            <?php endif; ?>
                                        </summary>
                                        <div class="detail">
                                            <?php if ($t['desc'] !== '' && $t['desc'] !== '_À compléter_'): ?>
                                                <div class="desc"><?= safeHtml($t['desc']) ?></div>
                                            <?php else: ?>
                                                <div class="no-desc">Aucune description renseignée.</div>
                                            <?php endif; ?>

                                            <?php if (!empty($t['subtasks'])): ?>
                                                <div class="sub-list">
                                                    <?php foreach ($t['subtasks'] as $st): ?>
                                                        <div style="margin-left: <?= $st['level'] * 18 ?>px;">
                                                            <span class="sub-check"><?= statusIconHtml($st['status']) ?></span>
                                                            <?php if ($st['id']): ?><span
                                                                    class="sub-id">[<?= htmlspecialchars($st['id']) ?>]</span><?php endif; ?>
                                                            <span><?= safeHtml($st['text']) ?></span>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="no-sub">Aucune sous-tâche.</div>
                                            <?php endif; ?>
                                        </div>
                                    </details>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </details>
                <?php endforeach; ?>
            </div>
        </details>
    <?php endforeach; ?>

</body>

</html>