<div class="card shadow-sm">
    <div class="card-body">
        <h4 class="mb-3">Edit Profile</h4>
        <form method="POST" action="/profile">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Age</label>
                    <input type="number" name="age" class="form-control" value="<?php echo htmlspecialchars($profile['age'] ?? ''); ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Education Level</label>
                    <input type="text" name="education_level" class="form-control" value="<?php echo htmlspecialchars($profile['education_level'] ?? ''); ?>">
                </div>
                <div class="col-md-5 mb-3">
                    <label class="form-label">Institution</label>
                    <input type="text" name="institution" class="form-control" value="<?php echo htmlspecialchars($profile['institution'] ?? ''); ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Graduation Year</label>
                    <input type="text" name="graduation_year" class="form-control" value="<?php echo htmlspecialchars($profile['graduation_year'] ?? ''); ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Skills (comma-separated)</label>
                    <input type="text" name="skills" id="skillsField" class="form-control" value="<?php echo htmlspecialchars($skills ?? ''); ?>">
                    <?php $skillCatalog = $skillCatalog ?? []; ?>
                    <?php if (!empty($skillCatalog)): ?>
                        <div class="input-group input-group-sm mt-2">
                            <input type="text" id="skillPicker" class="form-control" list="skillOptions"
                                   placeholder="Search <?php echo count($skillCatalog); ?> skills…" autocomplete="off">
                            <button type="button" class="btn btn-outline-secondary" id="addSkillBtn">Add</button>
                        </div>
                        <datalist id="skillOptions">
                            <?php foreach ($skillCatalog as $name): ?>
                                <option value="<?php echo htmlspecialchars($name); ?>"></option>
                            <?php endforeach; ?>
                        </datalist>
                        <small class="text-muted">Pick from <?php echo count($skillCatalog); ?> known skills, or type your own above.</small>
                    <?php endif; ?>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Interests (comma-separated)</label>
                    <input type="text" name="interests" class="form-control" value="<?php echo htmlspecialchars($interests ?? ''); ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Save Profile</button>
        </form>
    </div>
</div>

<script>
(function () {
    var picker = document.getElementById('skillPicker');
    var field = document.getElementById('skillsField');
    var btn = document.getElementById('addSkillBtn');
    if (!picker || !field || !btn) return;

    function addSkill() {
        var value = picker.value.trim();
        if (!value) return;
        var existing = field.value.split(',').map(function (s) { return s.trim().toLowerCase(); });
        if (existing.indexOf(value.toLowerCase()) === -1) {
            field.value = field.value.trim()
                ? field.value.replace(/,\s*$/, '') + ', ' + value
                : value;
        }
        picker.value = '';
        picker.focus();
    }

    btn.addEventListener('click', addSkill);
    picker.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); addSkill(); }
    });
})();
</script>
