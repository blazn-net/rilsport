<!-- Contenu Fiche / Formulaire Équipe -->
<main class="p-4" style="margin-top: 60px;">
    <?php
    $isViewMode = ($data['mode'] === 'view');
    $team       = $data['team'] ?? null;
    $allSections = $data['allSections'] ?? [];
    ?>

    <!-- Fil d'ariane & Bouton retour -->
    <div class="d-flex flex-justify-between flex-align-center flex-wrap mb-4">
        <div>
            <a href="<?php echo URLROOT; ?>/sport/teams" class="button light mr-2">
                <span class="mif-arrow-left"></span> <?php echo $data['txt']['SPORT_TEAMS_MGT'] ?? 'Équipes'; ?>
            </a>
            <span class="text-leader ml-2">
                <?php if ($isViewMode): ?>
                    <span class="mif-groups mr-1"></span> <?php echo htmlspecialchars($team->name ?? ''); ?>
                <?php else: ?>
                    <span class="mif-pencil mr-1"></span> <?php echo $team ? ($data['txt']['SPORT_EDIT_TEAM_TITLE'] ?? 'Modifier l\'équipe') : ($data['txt']['SPORT_ADD_TEAM_TITLE'] ?? 'Créer une équipe'); ?>
                <?php endif; ?>
            </span>
        </div>

        <?php if ($isViewMode && !empty($data['isAdmin'])): ?>
            <a href="<?php echo URLROOT; ?>/sport/team/<?php echo htmlspecialchars($team->id); ?>" class="button info">
                <span class="mif-pencil"></span> <?php echo $data['txt']['SYS_BTN_EDIT'] ?? 'Modifier'; ?>
            </a>
        <?php endif; ?>
    </div>

    <?php if (!empty($data['message'])): ?>
        <div class="remark success mb-3"><?php echo htmlspecialchars($data['message']); ?></div>
    <?php endif; ?>

    <?php if (!empty($data['error'])): ?>
        <div class="remark alert mb-3"><?php echo htmlspecialchars($data['error']); ?></div>
    <?php endif; ?>

    <?php if ($isViewMode && $team): ?>
        <!-- ============================================================ -->
        <!-- MODE CONSULTATION (VIEW) : PUR HTML, SANS INPUTS/SELECTS     -->
        <!-- ============================================================ -->
        <div class="row">
            <!-- Carte d'identité de l'équipe -->
            <div class="cell-md-5 mb-4">
                <div class="card p-4">
                    <div class="d-flex flex-align-center mb-3">
                        <div style="width: 50px; height: 50px; line-height: 50px; border-radius: 50%; background: <?php echo !empty($team->club_primary_color) ? htmlspecialchars($team->club_primary_color) : '#0072c6'; ?>; color: #fff; text-align: center; font-size: 22px;" class="mr-3">
                            <span class="mif-groups"></span>
                        </div>
                        <div>
                            <h4 class="mb-0"><?php echo htmlspecialchars($team->name); ?></h4>
                            <?php if (!empty($team->short_name)): ?>
                                <span class="fg-gray"><?php echo htmlspecialchars($team->short_name); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <p><code><?php echo htmlspecialchars($team->code); ?></code></p>
                    <hr class="thin my-3">

                    <p class="mb-2">
                        <span class="mif-security mr-2"></span><strong>Club :</strong>
                        <a href="<?php echo URLROOT; ?>/sport/club/<?php echo htmlspecialchars($team->club_id); ?>" class="fg-primary text-bold">
                            <?php echo htmlspecialchars($team->club_name); ?>
                        </a>
                        <small class="fg-gray">(<?php echo htmlspecialchars($team->club_city); ?>, <?php echo htmlspecialchars($team->club_country); ?>)</small>
                    </p>

                    <p class="mb-2">
                        <span class="<?php echo !empty($team->sport_icon) ? htmlspecialchars($team->sport_icon) : 'mif-trophy'; ?> mr-2"></span><strong>Discipline :</strong>
                        <?php echo htmlspecialchars($team->sport_name); ?>
                        <small class="fg-gray">(Section : <?php echo htmlspecialchars($team->section_name); ?>)</small>
                    </p>

                    <p class="mb-2">
                        <span class="mif-user mr-2"></span><strong>Genre :</strong>
                        <?php 
                            $gLbl = ($team->gender === 'F') ? 'Féminin' : (($team->gender === 'MIXED') ? 'Mixte' : 'Masculin');
                            $gBadge = ($team->gender === 'F') ? 'alert' : (($team->gender === 'MIXED') ? 'warning' : 'info');
                        ?>
                        <span class="badge <?php echo $gBadge; ?>"><?php echo htmlspecialchars($gLbl); ?></span>
                    </p>

                    <p class="mb-2">
                        <span class="mif-tag mr-2"></span><strong>Catégorie :</strong>
                        <span class="badge light"><?php echo htmlspecialchars($team->category); ?></span>
                    </p>

                    <p class="mb-2">
                        <span class="mif-chart-line mr-2"></span><strong>Niveau / Division :</strong>
                        <?php echo htmlspecialchars($team->level ?? 'Non renseigné'); ?>
                    </p>

                    <p class="mb-2">
                        <span class="mif-info mr-2"></span><strong>Statut :</strong>
                        <?php 
                            $sText = $data['txt'][$team->status_text_code] ?? 'Active';
                            $bClass = ((int)$team->status_id === 1) ? 'success' : 'secondary';
                        ?>
                        <span class="badge <?php echo $bClass; ?>"><?php echo htmlspecialchars($sText); ?></span>
                    </p>
                </div>
            </div>

            <!-- Colonne droite : Prochains matches / Effectif -->
            <div class="cell-md-7">
                <div class="card p-4 mb-4">
                    <h5><span class="mif-organization mr-2"></span>Rattachement & Structure</h5>
                    <p class="mt-2">
                        Cette équipe évolue sous les couleurs du <strong><?php echo htmlspecialchars($team->club_name); ?></strong> au sein de la discipline <strong><?php echo htmlspecialchars($team->sport_name); ?></strong>.
                    </p>
                    <div class="remark info mt-3">
                        <span class="mif-info mr-1"></span> Le module des compétitions et matches (Point B1-1 du TODO) permettra prochainement d'associer cette équipe à ses championnats, calendriers de matches et effectifs de joueurs.
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- ============================================================ -->
        <!-- MODE FORMULAIRE / ÉDITION (ADMIN)                           -->
        <!-- ============================================================ -->
        <form method="POST" action="<?php echo URLROOT; ?>/sport/team<?php echo $team ? '/' . $team->id : ''; ?>" id="teamForm">
            <div class="card p-4" style="max-width: 800px;">
                <h5><span class="mif-info mr-2"></span>Informations de l'Équipe</h5>
                <hr class="thin my-3">

                <!-- Choix de la Section rattachée (Club + Sport) -->
                <div class="form-group mb-3">
                    <label class="text-bold"><?php echo $data['txt']['TEAM_SECTION'] ?? 'Section / Club rattaché'; ?> <span class="fg-red">*</span></label>
                    <select name="section_id" data-role="select" required>
                        <option value="">-- Choisir la section (Club & Sport) --</option>
                        <?php foreach ($allSections as $sec): ?>
                            <option value="<?php echo htmlspecialchars($sec['id']); ?>" <?php echo ($team && $team->section_id == $sec['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($sec['club_name']); ?> — <?php echo htmlspecialchars($sec['sport_name']); ?> (<?php echo htmlspecialchars($sec['section_name']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-muted">L'équipe est directement liée à la section sportive du club concerné.</small>
                </div>

                <div class="row mb-3">
                    <!-- Code / Slug unique -->
                    <div class="cell-sm-6">
                        <label class="text-bold"><?php echo $data['txt']['TEAM_CODE'] ?? 'Code unique'; ?> <span class="fg-red">*</span></label>
                        <input type="text" name="code" data-role="input" required pattern="[a-z0-9_-]+" placeholder="ex: psg-foot-pro, asr-rugby-1" value="<?php echo htmlspecialchars($team->code ?? ''); ?>" <?php echo $team ? 'readonly' : ''; ?>>
                        <small class="text-muted">Minuscules, chiffres et tirets uniquement.</small>
                    </div>

                    <!-- Nom de l'équipe -->
                    <div class="cell-sm-6">
                        <label class="text-bold"><?php echo $data['txt']['TEAM_NAME'] ?? 'Nom de l\'équipe'; ?> <span class="fg-red">*</span></label>
                        <input type="text" name="name" data-role="input" required placeholder="ex: Équipe Première Pro, U19 Masculin" value="<?php echo htmlspecialchars($team->name ?? ''); ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <!-- Nom court -->
                    <div class="cell-sm-6">
                        <label class="text-bold"><?php echo $data['txt']['TEAM_SHORT_NAME'] ?? 'Nom court'; ?></label>
                        <input type="text" name="short_name" data-role="input" placeholder="ex: Équipe 1, PSG Pro" value="<?php echo htmlspecialchars($team->short_name ?? ''); ?>">
                    </div>

                    <!-- Genre -->
                    <div class="cell-sm-6">
                        <label class="text-bold"><?php echo $data['txt']['TEAM_GENDER'] ?? 'Genre'; ?></label>
                        <select name="gender" data-role="select">
                            <option value="M" <?php echo ($team && $team->gender === 'M') ? 'selected' : ''; ?>>Masculin</option>
                            <option value="F" <?php echo ($team && $team->gender === 'F') ? 'selected' : ''; ?>>Féminin</option>
                            <option value="MIXED" <?php echo ($team && $team->gender === 'MIXED') ? 'selected' : ''; ?>>Mixte</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <!-- Catégorie d'âge -->
                    <div class="cell-sm-6">
                        <label class="text-bold"><?php echo $data['txt']['TEAM_CATEGORY'] ?? 'Catégorie d\'âge'; ?></label>
                        <input type="text" name="category" data-role="input" placeholder="Senior, U21, U19, U17, Vétéran..." value="<?php echo htmlspecialchars($team->category ?? 'Senior'); ?>">
                    </div>

                    <!-- Niveau / Rang -->
                    <div class="cell-sm-6">
                        <label class="text-bold"><?php echo $data['txt']['TEAM_LEVEL'] ?? 'Niveau / Division'; ?></label>
                        <input type="text" name="level" data-role="input" placeholder="Professionnel, National, Régional..." value="<?php echo htmlspecialchars($team->level ?? 'National'); ?>">
                    </div>
                </div>

                <!-- Statut -->
                <div class="form-group mb-4">
                    <label class="text-bold"><?php echo $data['txt']['TEAM_STATUS'] ?? 'Statut'; ?></label>
                    <select name="status_id" data-role="select">
                        <?php foreach ($data['statuses'] as $st): ?>
                            <option value="<?php echo htmlspecialchars($st['id']); ?>" <?php echo ($team && $team->status_id == $st['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($data['txt'][$st['text_code']] ?? $st['text_code']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Boutons -->
                <div class="d-flex flex-align-center" style="gap: 10px;">
                    <button type="submit" class="button success large">
                        <span class="mif-floppy-disk mr-1"></span> <?php echo $data['txt']['SYS_BTN_SAVE'] ?? 'Enregistrer'; ?>
                    </button>
                    <a href="<?php echo URLROOT; ?>/sport/teams" class="button secondary large">
                        <?php echo $data['txt']['SYS_BTN_CANCEL'] ?? 'Annuler'; ?>
                    </a>
                </div>
            </div>
        </form>
    <?php endif; ?>
</main>
