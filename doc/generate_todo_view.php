<?php
/**
 * generate_todo_view.php
 *
 * Vue groupée (Type > Priorité) des issues GitHub du projet rilsport.
 * Source : https://github.com/blazn-net/rilsport/issues (API GitHub)
 */

const GH_TOKEN = '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';
const GH_REPO  = 'blazn-net/rilsport';
const GH_API   = 'https://api.github.com/repos/' . GH_REPO;
const CACHE_TTL = 300;

if (session_status() === PHP_SESSION_NONE) { session_start(); }

function ghApiGet(string $url): ?array {
    $opts = ['http' => ['method' => 'GET', 'header' => implode("\r\n", [
        'Authorization: token ' . GH_TOKEN,
        'Accept: application/vnd.github+json',
        'User-Agent: rilsport-todo/1.0',
        'X-GitHub-Api-Version: 2022-11-28',
    ]), 'timeout' => 10]];
    $json = @file_get_contents($url, false, stream_context_create($opts));
    return $json !== false ? json_decode($json, true) : null;
}

function fetchAllIssues(): array {
    $key = 'gh_issues_' . GH_REPO;
    $now = time();
    if (isset($_SESSION[$key], $_SESSION[$key.'_ts']) && ($now - $_SESSION[$key.'_ts']) < CACHE_TTL) {
        return $_SESSION[$key];
    }
    $all = []; $page = 1;
    while (true) {
        $data = ghApiGet(GH_API . '/issues?state=all&per_page=100&page=' . $page);
        if (!is_array($data) || !count($data)) break;
        foreach ($data as $i) { if (!isset($i['pull_request'])) $all[] = $i; }
        if (count($data) < 100) break;
        $page++;
    }
    $_SESSION[$key] = $all; $_SESSION[$key.'_ts'] = $now;
    return $all;
}

function parseLabels(array $labels): array {
    $type = ''; $priority = ''; $frontBack = ''; $inProgress = false;
    $typeMap = ['type: bug'=>'Bug','type: evolution'=>'Évolution','type: question'=>'Question / Idées','type: refactoring'=>'Refactoring','type: test'=>'Test'];
    $prioMap = ['priority: p1'=>'P1','priority: p2'=>'P2','priority: p3'=>'P3','priority: p4'=>'P4'];
    foreach ($labels as $l) {
        $n = strtolower($l['name']);
        if (isset($typeMap[$n])) $type = $typeMap[$n];
        elseif (isset($prioMap[$n])) $priority = $prioMap[$n];
        elseif ($n === 'front') $frontBack = 'Front';
        elseif ($n === 'back') $frontBack = 'Back';
        elseif ($n === 'in-progress') $inProgress = true;
    }
    return compact('type','priority','frontBack','inProgress');
}

function parseIssueBody(string $body): array {
    $body = str_replace(["\r\n","\r"],"\n",$body);
    $desc = ''; $subtasks = [];
    if (preg_match('/^##\s+Description\s*\n(.*?)(?=\n##\s+|\z)/ms', $body, $m)) {
        $desc = trim($m[1]);
        if ($desc === '_À compléter_') $desc = '';
    }
    $lines = explode("\n", $body); $inSub = false;
    foreach ($lines as $line) {
        if (preg_match('/^##\s+Sous-tâches/i', $line)) { $inSub = true; continue; }
        if ($inSub && preg_match('/^##\s+/i', $line)) break;
        if ($inSub && preg_match('/^(\s*)-\s+\[([ x\-]?)\]\s*(?:`\[(\d{8}-\d{4})\]`)?\s*(.*)$/u', $line, $sm)) {
            $subtasks[] = ['level'=>(int)floor(strlen($sm[1])/2),'status'=>$sm[2]==='x'?'[x]':($sm[2]==='-'?'[-]':'[ ]'),'id'=>$sm[3]??'','text'=>trim($sm[4]),'desc'=>''];
        }
    }
    return ['desc'=>$desc,'subtasks'=>$subtasks];
}

function extractIdFromTitle(string $title): array {
    if (preg_match('/^\[(\d{8}-\d{4})\]\s*(.+)$/', $title, $m)) return ['id'=>$m[1],'title'=>trim($m[2])];
    return ['id'=>'','title'=>$title];
}

function deriveStatus(string $state, bool $inProgress): string {
    if ($state === 'closed') return 'Réalisé';
    return $inProgress ? 'En cours' : 'Nouveau';
}

// Invalider cache
if (isset($_GET['refresh'])) {
    unset($_SESSION['gh_issues_'.GH_REPO], $_SESSION['gh_issues_'.GH_REPO.'_ts']);
    $qs = http_build_query(array_diff_key($_GET, ['refresh'=>1]));
    header('Location: ?'.($qs?:''));
    exit;
}

$rawIssues = fetchAllIssues();
$tasks = [];
foreach ($rawIssues as $issue) {
    $meta = parseLabels($issue['labels'] ?? []);
    $parsed = parseIssueBody($issue['body'] ?? '');
    $td = extractIdFromTitle($issue['title'] ?? '');
    $tasks[] = ['id'=>$td['id'],'title'=>$td['title'],'type'=>$meta['type'],'priority'=>$meta['priority'],
        'status'=>deriveStatus($issue['state'],$meta['inProgress']),'front_back'=>$meta['frontBack'],
        'desc'=>$parsed['desc'],'subtasks'=>$parsed['subtasks'],'gh_number'=>$issue['number'],'gh_url'=>$issue['html_url']];
}

$options = array_filter(['status'=>$_GET['status']??null,'type'=>$_GET['type']??null,'priority'=>$_GET['priority']??null,'front_back'=>$_GET['front_back']??null],fn($v)=>$v!==null&&$v!=='');
foreach (['status','type','priority','front_back'] as $fk) {
    if (!empty($options[$fk])) { $w=array_map('trim',explode(',',$options[$fk])); $tasks=array_values(array_filter($tasks,fn($t)=>in_array($t[$fk],$w,true))); }
}

$grouped = [];
foreach ($tasks as $t) { $grouped[$t['type']!==''?$t['type']:'(Type non défini)'][$t['priority']!==''?$t['priority']:'(Priorité non définie)'][] = $t; }
$typeOrder=['Bug'=>1,'Évolution'=>2,'Question / Idées'=>3,'Refactoring'=>4,'Test'=>5];
$prioOrder=['P1'=>1,'P2'=>2,'P3'=>3,'P4'=>4];
uksort($grouped,fn($a,$b)=>($typeOrder[$a]??99)<=>($typeOrder[$b]??99));
foreach ($grouped as &$bp) { uksort($bp,fn($a,$b)=>($prioOrder[$a]??99)<=>($prioOrder[$b]??99)); }
unset($bp);

function filterUrl(array $ov): string {
    $p=$_GET; foreach($ov as $k=>$v){if($v===null)unset($p[$k]);else $p[$k]=$v;} $q=http_build_query($p); return $q===''?'?':'?'.$q;
}
function statusIconHtml(string $r): string {
    return match(trim($r)){'[x]'=>'<span class="ico ico-done" title="Réalisé">✔</span>','[-]'=>'<span class="ico ico-progress" title="En cours">●</span>',default=>'<span class="ico ico-todo" title="À faire">○</span>'};
}
function statusIcon(string $s): string { return match($s){'Réalisé'=>'[x]','En cours'=>'[-]',default=>'[ ]'}; }
function safeHtml(string $t): string {
    $t=strip_tags($t,'<strong><b><em><i><u><code><br><p>');
    $t=preg_replace('/<(strong|b|em|i|u|code|p)\b[^>]*>/i','<$1>',$t);
    return preg_replace('/<br\b[^>]*\/?>/i','<br>',$t);
}
function applyInlineMarkdown(string $t): string {
    $t=preg_replace('/`([^`]+)`/','<code>$1</code>',$t);
    $t=preg_replace('/\*\*([^*]+)\*\*/','<strong>$1</strong>',$t);
    return preg_replace('/(?<!\*)\*(?!\*)([^*]+)(?<!\*)\*(?!\*)/','<em>$1</em>',$t);
}
function formatDesc(string $text): string {
    $lines=explode("\n",$text); $parts=[]; $result=''; $buf=[];
    foreach($lines as $line) {
        if(preg_match('/^(\s*)-\s+(.*)$/',$line,$m)) { $level=(int)floor(strlen($m[1])/2); $parts[]=['type'=>'item','level'=>$level,'content'=>applyInlineMarkdown(safeHtml($m[2]))]; }
        else $parts[]=['type'=>'text','content'=>applyInlineMarkdown(safeHtml($line))];
    }
    foreach($parts as $p) {
        if($p['type']==='item') { if($buf){$result.=nl2br(implode("\n",$buf));$buf=[];} $result.='<div class="desc-item" style="margin-left:'.($p['level']*16).'px">&bull;&nbsp;<span class="desc-item-text">'.$p['content'].'</span></div>'; }
        else $buf[]=$p['content'];
    }
    if($buf) $result.=nl2br(implode("\n",$buf));
    return $result;
}

header('Content-Type: text/html; charset=utf-8');
$statusColor=['Nouveau'=>'#0072c6','En cours'=>'#e3a21a','Réalisé'=>'#4caf50'];
$typeColor=['Bug'=>'#d9534f','Évolution'=>'#0072c6','Question / Idées'=>'#8e44ad','Refactoring'=>'#f39c12','Test'=>'#16a085'];
$typePlural=['Bug'=>'Bugs','Évolution'=>'Évolutions','Question / Idées'=>'Questions / Idées','Refactoring'=>'Refactoring','Test'=>'Tests'];
$curStatus=$_GET['status']??''; $curType=$_GET['type']??''; $curPrio=$_GET['priority']??''; $curFb=$_GET['front_back']??'';
$afc=($curType!==''?1:0)+($curPrio!==''?1:0)+($curStatus!==''?1:0)+($curFb!==''?1:0);
$cacheAge=isset($_SESSION['gh_issues_'.GH_REPO.'_ts'])?(time()-$_SESSION['gh_issues_'.GH_REPO.'_ts']):null;
?><!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>TODO rilsport — GitHub Issues</title>
<style>
body{font-family:Segoe UI,Arial,sans-serif;max-width:900px;margin:30px auto;padding:0 20px;color:#222;background:#f7f7f8;}
h1{font-size:22px;margin-bottom:4px;}
.meta{color:#777;font-size:13px;margin-bottom:24px;}.meta a{color:#0072c6;}
.cache-info{font-size:11px;color:#aaa;margin-left:6px;}
.type-section{margin-top:20px;}
.type-summary{display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:6px;color:#fff;font-size:16px;font-weight:600;cursor:pointer;list-style:none;}
.type-summary::-webkit-details-marker{display:none;}
.type-summary::before{content:'▾';font-size:13px;transition:transform .15s;}
.type-section:not([open]) .type-summary::before{content:'▸';}
.type-count{font-weight:400;opacity:.85;font-size:14px;}.type-body{padding-top:4px;}
.prio-section{margin-top:18px;}
.prio-summary{font-size:14px;text-transform:uppercase;color:#555;letter-spacing:.5px;font-weight:600;cursor:pointer;list-style:none;padding:4px 0;}
.prio-summary::-webkit-details-marker{display:none;}
.prio-summary::before{content:'▾ ';font-size:11px;color:#999;}
.prio-section:not([open]) .prio-summary::before{content:'▸ ';}
.prio-count{color:#bbb;font-weight:400;text-transform:none;}
ul{list-style:none;padding-left:0;margin:0 0 10px 0;}
li{background:#fff;border:1px solid #e3e3e3;border-radius:6px;margin-bottom:6px;overflow:hidden;}
summary{display:flex;align-items:center;gap:10px;padding:8px 12px;cursor:pointer;list-style:none;}
summary::-webkit-details-marker{display:none;}
summary::before{content:'▸';color:#999;font-size:11px;width:10px;flex-shrink:0;transition:transform .15s;}
details[open] summary::before{transform:rotate(90deg);}
details[open] summary{border-bottom:1px solid #eee;background:#fafafa;}
.id{font-family:Consolas,monospace;font-size:11px;color:#999;white-space:nowrap;}
.title{flex:1;font-size:14px;}
.badge{font-size:11px;padding:2px 8px;border-radius:10px;color:#fff;white-space:nowrap;}
.check{width:20px;text-align:center;flex-shrink:0;}
.ico{display:inline-block;font-size:15px;font-weight:bold;}
.ico-done{color:#2e7d32;}.ico-progress{color:#e3a21a;}.ico-todo{color:#ccc;}
.gh-link{font-size:10px;color:#aaa;text-decoration:none;flex-shrink:0;font-family:Consolas,monospace;}
.gh-link:hover{color:#0072c6;}
.empty{color:#999;font-style:italic;padding:8px 0;}
.detail{padding:10px 14px 12px 32px;font-size:13px;}
.detail .desc{color:#444;margin-bottom:8px;line-height:1.5;}
.detail .no-desc{color:#aaa;font-style:italic;margin-bottom:8px;}
.sub-list{margin:0;padding:0;}.sub-item{margin:2px 0 4px 0;}
.sub-row{display:flex;align-items:baseline;gap:8px;padding:2px 0;color:#333;}
.sub-list .sub-check{width:16px;flex-shrink:0;}
.sub-list .sub-id{font-family:Consolas,monospace;font-size:10px;color:#bbb;flex-shrink:0;}
.sub-text{flex:1;}.no-sub{color:#aaa;font-style:italic;}
.desc-item{display:flex;align-items:baseline;gap:5px;padding:1px 0;font-size:13px;color:#444;line-height:1.5;}
.desc-item-text{flex:1;}
.filter-section{background:#fff;border:1px solid #e3e3e3;border-radius:6px;margin-bottom:20px;}
.filter-summary{padding:10px 14px;font-size:13px;font-weight:600;color:#444;cursor:pointer;list-style:none;}
.filter-summary::-webkit-details-marker{display:none;}
.filter-summary::before{content:'▾ ';font-size:11px;color:#999;}
.filter-section:not([open]) .filter-summary::before{content:'▸ ';}
.filter-section[open] .filter-summary{border-bottom:1px solid #eee;}
.filter-active-count{font-weight:400;color:#0072c6;}
.filter-bar{padding:12px 14px;}
.filter-group{display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:8px;}
.filter-group:last-child{margin-bottom:0;}
.filter-label{font-size:12px;color:#888;width:70px;flex-shrink:0;text-transform:uppercase;letter-spacing:.3px;}
.pill{font-size:12px;padding:4px 11px;border-radius:12px;text-decoration:none;border:1px solid #ddd;color:#555;background:#fafafa;white-space:nowrap;}
.pill:hover{border-color:#999;}.pill.active{color:#fff;background:#555;border-color:transparent;}
.pill.c-fb-Front{color:#d84733;border-color:#f8b8ae;background:#fef2f0;}
.pill.c-fb-Front.active{background:#F15E4A;color:#fff;}
.pill.c-fb-Back{color:#5c595a;border-color:#dad8d9;background:#f4f3f3;}
.pill.c-fb-Back.active{background:#767374;color:#fff;}
.pill.c-status-Nouveau{color:#0072c6;border-color:#b9dcf2;background:#eef7fd;}
.pill.c-status-Nouveau.active{background:#0072c6;color:#fff;}
.pill.c-status-Encours{color:#b9791a;border-color:#f2ddb0;background:#fdf6e8;}
.pill.c-status-Encours.active{background:#e3a21a;color:#fff;}
.pill.c-status-Réalisé{color:#2e7d32;border-color:#bfe3c1;background:#eef8ef;}
.pill.c-status-Réalisé.active{background:#4caf50;color:#fff;}
.pill.c-prio-P1{color:#c0392b;border-color:#f0c4bf;background:#fdf1f0;}
.pill.c-prio-P1.active{background:#e74c3c;color:#fff;}
.pill.c-prio-P2{color:#c46a12;border-color:#f2d7b9;background:#fdf3ea;}
.pill.c-prio-P2.active{background:#e67e22;color:#fff;}
.pill.c-prio-P3{color:#ab8a0c;border-color:#f0e3ae;background:#fdf9ea;}
.pill.c-prio-P3.active{background:#d4a017;color:#fff;}
.pill.c-prio-P4{color:#6c7a7a;border-color:#d8dede;background:#f4f6f6;}
.pill.c-prio-P4.active{background:#7f8c8d;color:#fff;}
.pill.c-type-Bug{color:#c0392b;border-color:#f0c4bf;background:#fdf1f0;}
.pill.c-type-Bug.active{background:#d9534f;color:#fff;}
.pill.c-type-Évolution{color:#0072c6;border-color:#b9dcf2;background:#eef7fd;}
.pill.c-type-Évolution.active{background:#0072c6;color:#fff;}
.pill.c-type-QuestionIdées{color:#8e44ad;border-color:#ddc2e8;background:#f8f0fa;}
.pill.c-type-QuestionIdées.active{background:#8e44ad;color:#fff;}
.pill.c-type-Refactoring{color:#d35400;border-color:#edbb99;background:#fdf2e9;}
.pill.c-type-Refactoring.active{background:#e67e22;color:#fff;}
.pill.c-type-Test{color:#16a085;border-color:#a3e4d7;background:#e8f8f5;}
.pill.c-type-Test.active{background:#16a085;color:#fff;}
.pill.c-tout.active{background:#333;color:#fff;}
.toolbar{position:sticky;top:10px;float:right;display:flex;gap:6px;margin:-4px 0 10px 16px;z-index:100;}
.toolbar-btn{display:inline-flex;align-items:center;gap:5px;padding:5px 11px;border-radius:20px;font-size:12px;font-weight:600;cursor:pointer;white-space:nowrap;text-decoration:none;border:1px solid transparent;transition:background .15s,box-shadow .15s;box-shadow:0 1px 4px rgba(0,0,0,.12);}
.toolbar-btn-reset,.toolbar-btn-collapse,.toolbar-btn-refresh{background:#fff;color:#555;border-color:#ddd;}
.toolbar-btn-reset:hover,.toolbar-btn-collapse:hover,.toolbar-btn-refresh:hover{background:#f0f0f0;box-shadow:0 2px 6px rgba(0,0,0,.15);}
.toolbar-btn-gh{background:#24292f;color:#fff;}.toolbar-btn-gh:hover{background:#444;}
</style>
</head>
<body>
<div class="toolbar">
    <a class="toolbar-btn toolbar-btn-gh" href="https://github.com/<?= GH_REPO ?>/issues" target="_blank">⬡ GitHub</a>
    <a class="toolbar-btn toolbar-btn-refresh" href="?refresh=1" title="Vider le cache et recharger">↺ Actualiser</a>
    <a class="toolbar-btn toolbar-btn-reset" href="?" title="Supprimer tous les filtres">✕ Réinitialiser</a>
    <button class="toolbar-btn toolbar-btn-collapse" id="btn-collapse-all">⊖ Tout replier</button>
</div>

<h1>📋 TODO rilsport</h1>
<div class="meta">
    Source : <a href="https://github.com/<?= GH_REPO ?>/issues" target="_blank">GitHub Issues</a>
    — <?= count($tasks) ?> tâche(s) affichée(s).
    <span class="cache-info">
        <?php if ($cacheAge !== null): ?>
            (cache : <?= $cacheAge < 60 ? $cacheAge.'s' : round($cacheAge/60).' min' ?> — <a href="?refresh=1">actualiser</a>)
        <?php endif; ?>
    </span>
</div>

<details class="filter-section"<?= $afc > 0 ? ' open' : '' ?>>
    <summary class="filter-summary">Filtres<?= $afc > 0 ? ' <span class="filter-active-count">('.$afc.' actif'.($afc>1?'s':'').')</span>' : '' ?></summary>
    <div class="filter-bar">
        <div class="filter-group">
            <span class="filter-label">Type</span>
            <a class="pill <?= $curType==='' ? 'active c-tout' : '' ?>" href="<?= filterUrl(['type'=>null]) ?>">Tout</a>
            <a class="pill <?= $curType==='Bug' ? 'active' : '' ?> c-type-Bug" href="<?= filterUrl(['type'=>'Bug']) ?>">Bugs</a>
            <a class="pill <?= $curType==='Évolution' ? 'active' : '' ?> c-type-Évolution" href="<?= filterUrl(['type'=>'Évolution']) ?>">Évolutions</a>
            <a class="pill <?= $curType==='Question / Idées' ? 'active' : '' ?> c-type-QuestionIdées" href="<?= filterUrl(['type'=>'Question / Idées']) ?>">Questions / Idées</a>
            <a class="pill <?= $curType==='Refactoring' ? 'active' : '' ?> c-type-Refactoring" href="<?= filterUrl(['type'=>'Refactoring']) ?>">Refactoring</a>
            <a class="pill <?= $curType==='Test' ? 'active' : '' ?> c-type-Test" href="<?= filterUrl(['type'=>'Test']) ?>">Tests</a>
        </div>
        <div class="filter-group">
            <span class="filter-label">Priorité</span>
            <a class="pill <?= $curPrio==='' ? 'active c-tout' : '' ?>" href="<?= filterUrl(['priority'=>null]) ?>">Tout</a>
            <?php foreach (['P1','P2','P3','P4'] as $p): ?>
                <a class="pill <?= $curPrio===$p ? 'active' : '' ?> c-prio-<?= $p ?>" href="<?= filterUrl(['priority'=>$p]) ?>"><?= $p ?></a>
            <?php endforeach; ?>
        </div>
        <div class="filter-group">
            <span class="filter-label">Statut</span>
            <a class="pill <?= $curStatus==='' ? 'active c-tout' : '' ?>" href="<?= filterUrl(['status'=>null]) ?>">Tout</a>
            <a class="pill <?= $curStatus==='Nouveau' ? 'active' : '' ?> c-status-Nouveau" href="<?= filterUrl(['status'=>'Nouveau']) ?>">○ Nouveau</a>
            <a class="pill <?= $curStatus==='En cours' ? 'active' : '' ?> c-status-Encours" href="<?= filterUrl(['status'=>'En cours']) ?>">● En cours</a>
            <a class="pill <?= $curStatus==='Réalisé' ? 'active' : '' ?> c-status-Réalisé" href="<?= filterUrl(['status'=>'Réalisé']) ?>">✔ Réalisé</a>
        </div>
        <div class="filter-group">
            <span class="filter-label">Front / Back</span>
            <a class="pill <?= $curFb==='' ? 'active c-tout' : '' ?>" href="<?= filterUrl(['front_back'=>null]) ?>">Tout</a>
            <a class="pill <?= $curFb==='Front' ? 'active' : '' ?> c-fb-Front" href="<?= filterUrl(['front_back'=>'Front']) ?>">🖥 Front</a>
            <a class="pill <?= $curFb==='Back' ? 'active' : '' ?> c-fb-Back" href="<?= filterUrl(['front_back'=>'Back']) ?>">⚙ Back</a>
        </div>
    </div>
</details>

<?php if (empty($grouped)): ?>
    <p class="empty">Aucune tâche ne correspond aux filtres actuels.</p>
<?php endif; ?>

<?php foreach ($grouped as $type => $byPrio): ?>
    <?php $typeTotal = array_sum(array_map('count', $byPrio)); ?>
    <details class="type-section" open>
        <summary class="type-summary" style="background:<?= $typeColor[$type] ?? '#666' ?>;">
            <span class="type-name"><?= htmlspecialchars($typePlural[$type] ?? $type) ?></span>
            <span class="type-count">(<?= $typeTotal ?>)</span>
        </summary>
        <div class="type-body">
            <?php foreach ($byPrio as $prio => $items): ?>
                <details class="prio-section" open>
                    <summary class="prio-summary"><?= htmlspecialchars($prio) ?> <span class="prio-count">(<?= count($items) ?>)</span></summary>
                    <ul>
                        <?php foreach ($items as $t): ?>
                            <li>
                                <details>
                                    <summary>
                                        <span class="check"><?= statusIconHtml(statusIcon($t['status'])) ?></span>
                                        <?php if ($t['id']): ?><span class="id">[<?= htmlspecialchars($t['id']) ?>]</span><?php endif; ?>
                                        <span class="title"><?= safeHtml($t['title']) ?></span>
                                        <?php if ($t['status']): ?>
                                            <span class="badge" style="background:<?= $statusColor[$t['status']] ?? '#999' ?>;"><?= htmlspecialchars($t['status']) ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($t['front_back'])): ?>
                                            <span class="badge" style="background:<?= $t['front_back']==='Front'?'#F15E4A':'#767374' ?>;"><?= $t['front_back']==='Front'?'🖥 Front':'⚙ Back' ?></span>
                                        <?php endif; ?>
                                        <a class="gh-link" href="<?= htmlspecialchars($t['gh_url']) ?>" target="_blank" title="Issue #<?= $t['gh_number'] ?> sur GitHub" onclick="event.stopPropagation()">#<?= $t['gh_number'] ?></a>
                                    </summary>
                                    <div class="detail">
                                        <?php if ($t['desc'] !== ''): ?>
                                            <div class="desc"><?= formatDesc($t['desc']) ?></div>
                                        <?php else: ?>
                                            <div class="no-desc">Aucune description renseignée.</div>
                                        <?php endif; ?>
                                        <?php if (!empty($t['subtasks'])): ?>
                                            <div class="sub-list">
                                                <?php foreach ($t['subtasks'] as $st): ?>
                                                    <div class="sub-item" style="margin-left:<?= $st['level']*18 ?>px;">
                                                        <div class="sub-row">
                                                            <span class="sub-check"><?= statusIconHtml($st['status']) ?></span>
                                                            <?php if ($st['id']): ?><span class="sub-id">[<?= htmlspecialchars($st['id']) ?>]</span><?php endif; ?>
                                                            <span class="sub-text"><?= safeHtml($st['text']) ?></span>
                                                        </div>
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
<script>
(function(){
    document.getElementById('btn-collapse-all').addEventListener('click',function(){
        document.querySelectorAll('details[open]').forEach(function(el){el.removeAttribute('open');});
    });
})();
</script>
</html>
