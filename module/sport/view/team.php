<?php
/**
 * Vue : Équipe (Consultation View & Formulaire Add/Edit)
 * Utilise le template parent universel form_template.php
 */

$team        = $data['team'] ?? null;
$allSections = $data['allSections'] ?? [];

$clubColor = (!empty($team->club_primary_color) && strtolower($team->club_primary_color) !== '#ffffff' && strtolower($team->club_primary_color) !== '#fff')
    ? htmlspecialchars($team->club_primary_color) : '#0072c6';

$avatarHtml = '<div style="width: 52px; height: 52px; min-width: 52px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: ' . $clubColor . '; color: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.15); border: 2px solid #fff;">'
            . '<span class="mif-groups mif-2x"></span></div>';

$formConfig = [
    'mode'           => $data['mode'] ?? 'view',
    'item'           => $team,
    'icon'           => 'mif-groups',
    'viewTitle'      => 'Fiche de l\'équipe : ' . htmlspecialchars($team->name ?? ''),
    'editTitle'      => $data['txt']['SPORT_EDIT_TEAM_TITLE'] ?? 'Modifier l\'équipe',
    'addTitle'       => $data['txt']['SPORT_ADD_TEAM_TITLE'] ?? 'Créer une équipe',
    'backUrl'        => URLROOT . '/sport/teams',
    'editUrl'        => isset($team->id) ? URLROOT . '/sport/team/edit/' . (int)$team->id : null,
    'cancelUrl'      => isset($team->id) ? URLROOT . '/sport/team/' . (int)$team->id : URLROOT . '/sport/teams',
    'formAction'     => URLROOT . '/sport/team' . (($data['mode'] === 'edit' && isset($team->id)) ? '/' . (int)$team->id : ''),
    'viewAvatarHtml' => $avatarHtml,

    // Contenu spécifique du mode View (Consultation pure)
    'viewContent' => function($item, $data) { 
        $gLbl = ($item->gender === 'F') ? 'Féminin' : (($item->gender === 'MIXED') ? 'Mixte' : 'Masculin');
        $gBadge = ($item->gender === 'F') ? 'alert' : (($item->gender === 'MIXED') ? 'warning' : 'info');
    ?>
        <div class="row">
            <div class="cell-md-6 mb-3">
                <div class="p-3" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
                    <h5 class="text-bold mb-3" style="font-size: 14px; color: #475569;">
                        <span class="mif-organization mr-1"></span> Rattachement & Discipline
                    </h5>
                    
                    <p class="mb-2">
                        <span class="mif-security fg-primary mr-1"></span> <strong>Club :</strong>
                        <a href="<?php echo URLROOT; ?>/sport/club/<?php echo (int)($item->club_id ?? 0); ?>" class="fg-primary text-bold">
                            <?php echo htmlspecialchars($item->club_name ?? ''); ?>
                        </a>
                        <small class="fg-gray">(<?php echo htmlspecialchars($item->club_city ?? ''); ?>, <?php echo htmlspecialchars($item->club_country ?? ''); ?>)</small>
                    </p>

                    <p class="mb-0">
                        <span class="<?php echo !empty($item->sport_icon) ? htmlspecialchars($item->sport_icon) : 'mif-trophy'; ?> mr-1"></span> <strong>Discipline :</strong>
                        <?php echo htmlspecialchars($item->sport_name ?? ''); ?>
                        <small class="fg-gray">(Section : <?php echo htmlspecialchars($item->section_name ?? ''); ?>)</small>
                    </p>
                </div>
            </div>

            <div class="cell-md-6 mb-3">
                <div class="p-3" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
                    <h5 class="text-bold mb-3" style="font-size: 14px; color: #475569;">
                        <span class="mif-tag mr-1"></span> Caractéristiques
                    </h5>

                    <p class="mb-2">
                        <strong>Genre :</strong>
                        <span class="badge <?php echo $gBadge; ?>"><?php echo htmlspecialchars($gLbl); ?></span>
                    </p>

                    <p class="mb-2">
                        <strong>Catégorie :</strong>
                        <span class="badge light"><?php echo htmlspecialchars($item->category ?? 'Senior'); ?></span>
                    </p>

                    <p class="mb-0">
                        <strong>Niveau / Division :</strong>
                        <?php echo htmlspecialchars($item->level ?? 'Non renseigné'); ?>
                    </p>
                </div>
            </div>
        </div>
    <?php },

    // Contenu spécifique du mode Edit / Add (Formulaire interactif)
    'formContent' => function($item, $data, $mode) use ($allSections) { ?>
        <!-- Choix de la Section rattachée (Club + Sport) -->
        <div class="form-group mb-3">
            <label class="text-bold"><?php echo $data['txt']['TEAM_SECTION'] ?? 'Section / Club rattaché'; ?> <span class="fg-red">*</span></label>
            <select name="section_id" data-role="select" required>
                <option value="">-- Choisir la section (Club & Sport) --</option>
                <?php foreach ($allSections as $sec): ?>
                    <option value="<?php echo htmlspecialchars($sec['id']); ?>" <?php echo ($item && (int)$item->section_id === (int)$sec['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($sec['club_name']); ?> — <?php echo htmlspecialchars($sec['sport_name']); ?> (<?php echo htmlspecialchars($sec['section_name']); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            <small class="text-muted d-block mt-1">L'équipe est directement liée à la section sportive du club concerné.</small>
        </div>

        <div class="row mb-3">
            <!-- Code unique -->
            <div class="cell-sm-6">
                <label class="text-bold"><?php echo $data['txt']['TEAM_CODE'] ?? 'Code unique'; ?> <span class="fg-red">*</span></label>
                <input type="text" name="code" data-role="input" required pattern="[a-z0-9_-]+" placeholder="ex: psg-foot-pro, asr-rugby-1"
                       value="<?php echo htmlspecialchars($item->code ?? ''); ?>" <?php echo ($mode === 'edit') ? 'readonly' : ''; ?>>
                <?php if ($mode === 'edit'): ?>
                    <small class="fg-gray d-block mt-1"><span class="mif-lock mr-1"></span> Le code ne peut plus être modifié.</small>
                <?php else: ?>
                    <small class="text-muted d-block mt-1">Minuscules, chiffres et tirets uniquement.</small>
                <?php endif; ?>
            </div>

            <!-- Nom de l'équipe -->
            <div class="cell-sm-6">
                <label class="text-bold"><?php echo $data['txt']['TEAM_NAME'] ?? 'Nom de l\'équipe'; ?> <span class="fg-red">*</span></label>
                <input type="text" name="name" data-role="input" required placeholder="ex: Équipe Première Pro, U19 Masculin"
                       value="<?php echo htmlspecialchars($item->name ?? ''); ?>">
            </div>
        </div>

        <div class="row mb-3">
            <!-- Nom court -->
            <div class="cell-sm-6">
                <label class="text-bold"><?php echo $data['txt']['TEAM_SHORT_NAME'] ?? 'Nom court'; ?></label>
                <input type="text" name="short_name" data-role="input" placeholder="ex: Équipe 1, PSG Pro"
                       value="<?php echo htmlspecialchars($item->short_name ?? ''); ?>">
            </div>

            <!-- Genre -->
            <div class="cell-sm-6">
                <label class="text-bold"><?php echo $data['txt']['TEAM_GENDER'] ?? 'Genre'; ?></label>
                <select name="gender" data-role="select">
                    <option value="M" <?php echo ($item && $item->gender === 'M') ? 'selected' : ''; ?>>Masculin</option>
                    <option value="F" <?php echo ($item && $item->gender === 'F') ? 'selected' : ''; ?>>Féminin</option>
                    <option value="MIXED" <?php echo ($item && $item->gender === 'MIXED') ? 'selected' : ''; ?>>Mixte</option>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <!-- Catégorie d'âge -->
            <div class="cell-sm-6">
                <label class="text-bold"><?php echo $data['txt']['TEAM_CATEGORY'] ?? 'Catégorie d\'âge'; ?></label>
                <input type="text" name="category" data-role="input" placeholder="Senior, U21, U19, U17, Vétéran..."
                       value="<?php echo htmlspecialchars($item->category ?? 'Senior'); ?>">
            </div>

            <!-- Niveau / Rang -->
            <div class="cell-sm-6">
                <label class="text-bold"><?php echo $data['txt']['TEAM_LEVEL'] ?? 'Niveau / Division'; ?></label>
                <input type="text" name="level" data-role="input" placeholder="Professionnel, National, Régional..."
                       value="<?php echo htmlspecialchars($item->level ?? 'National'); ?>">
            </div>
        </div>

        <?php if ($mode === 'edit'): ?>
        <div class="form-group mb-3">
            <label class="text-bold"><?php echo $data['txt']['TEAM_STATUS'] ?? 'Statut'; ?></label>
            <select name="status_id" data-role="select">
                <?php foreach ($data['statuses'] as $st): ?>
                    <option value="<?php echo htmlspecialchars($st['id']); ?>" <?php echo ($item && (int)$item->status_id === (int)$st['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($data['txt'][$st['text_code']] ?? $st['text_code']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>
    <?php }
];

require 'module/system/view/common/form_template.php';
