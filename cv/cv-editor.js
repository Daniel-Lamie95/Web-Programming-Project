function createInput(name, placeholder, value, type) {
    var input = document.createElement('input');
    input.type = type || 'text';
    input.name = name;
    input.placeholder = placeholder || '';
    input.value = value || '';
    input.className = '';
    return input;
}

function createBulletRow(sectionPrefix, idx, val) {
    var row = document.createElement('div');
    row.className = 'cv-bullet-row';

    var input = document.createElement('input');
    input.type = 'text';
    input.name = sectionPrefix + '[' + idx + '][bullets][]';
    input.placeholder = 'Bullet point';
    input.value = val || '';

    var removeBtn = document.createElement('button');
    removeBtn.type = 'button';
    removeBtn.className = 'cv-bullet-remove';
    removeBtn.innerHTML = '&times;';
    removeBtn.onclick = function () {
        row.remove();
    };

    row.appendChild(input);
    row.appendChild(removeBtn);
    return row;
}

function addBullet(button, sectionPrefix, idx) {
    var container = button.previousElementSibling;
    var row = createBulletRow(sectionPrefix, idx, '');
    container.appendChild(row);
}

function addEducation() {
    var container = document.getElementById('education-entries');
    var idx = container.children.length;

    var entry = document.createElement('div');
    entry.className = 'cv-entry';
    entry.innerHTML =
        '<div class="cv-entry-header"><span>Education #' + (idx + 1) + '</span>' +
        '<button type="button" class="cv-btn-remove" onclick="this.closest(\'.cv-entry\').remove()">Remove</button></div>' +
        '<div class="cv-form-row">' +
        '<div class="cv-form-group"><label>School / University</label><input type="text" name="education[' + idx + '][school]" placeholder="e.g. MIT"></div>' +
        '<div class="cv-form-group"><label>Degree</label><input type="text" name="education[' + idx + '][degree]" placeholder="e.g. B.Sc. Computer Science"></div>' +
        '</div>' +
        '<div class="cv-form-row">' +
        '<div class="cv-form-group"><label>Location</label><input type="text" name="education[' + idx + '][location]" placeholder="City, Country"></div>' +
        '<div class="cv-form-group"><label>GPA</label><input type="text" name="education[' + idx + '][gpa]" placeholder="e.g. 3.8/4.0"></div>' +
        '</div>' +
        '<div class="cv-form-row">' +
        '<div class="cv-form-group"><label>Start Date</label><input type="text" name="education[' + idx + '][start_date]" placeholder="e.g. Sep 2020"></div>' +
        '<div class="cv-form-group"><label>End Date</label><input type="text" name="education[' + idx + '][end_date]" placeholder="e.g. Jun 2024"></div>' +
        '</div>' +
        '<div class="cv-form-row full">' +
        '<div class="cv-form-group"><label>Description</label><textarea name="education[' + idx + '][description]" placeholder="Relevant coursework, honors, etc."></textarea></div>' +
        '</div>';

    container.appendChild(entry);
}

function addExperience() {
    var container = document.getElementById('experience-entries');
    var idx = container.children.length;

    var entry = document.createElement('div');
    entry.className = 'cv-entry';
    entry.innerHTML =
        '<div class="cv-entry-header"><span>Experience #' + (idx + 1) + '</span>' +
        '<button type="button" class="cv-btn-remove" onclick="this.closest(\'.cv-entry\').remove()">Remove</button></div>' +
        '<div class="cv-form-row">' +
        '<div class="cv-form-group"><label>Company</label><input type="text" name="experience[' + idx + '][company]" placeholder="Company name"></div>' +
        '<div class="cv-form-group"><label>Position</label><input type="text" name="experience[' + idx + '][position]" placeholder="Job title"></div>' +
        '</div>' +
        '<div class="cv-form-row">' +
        '<div class="cv-form-group"><label>Location</label><input type="text" name="experience[' + idx + '][location]" placeholder="City, Country"></div>' +
        '<div class="cv-form-group"><label>Start Date</label><input type="text" name="experience[' + idx + '][start_date]" placeholder="e.g. Jan 2023"></div>' +
        '</div>' +
        '<div class="cv-form-row">' +
        '<div class="cv-form-group">' +
        '<div class="cv-checkbox-row"><input type="checkbox" name="experience[' + idx + '][current]" value="1" onchange="toggleEndDate(this)"><label>Currently working here</label></div>' +
        '</div>' +
        '<div class="cv-form-group"><label>End Date</label><input type="text" name="experience[' + idx + '][end_date]" placeholder="e.g. Dec 2023"></div>' +
        '</div>' +
        '<div class="cv-form-row full"><div class="cv-form-group"><label>Bullet Points</label>' +
        '<div class="cv-bullets-container"></div>' +
        '<button type="button" class="cv-btn-add-bullet" onclick="addBullet(this, \'experience\', ' + idx + ')">+ Add Bullet</button>' +
        '</div></div>';

    container.appendChild(entry);
}

function addProject() {
    var container = document.getElementById('project-entries');
    var idx = container.children.length;

    var entry = document.createElement('div');
    entry.className = 'cv-entry';
    entry.innerHTML =
        '<div class="cv-entry-header"><span>Project #' + (idx + 1) + '</span>' +
        '<button type="button" class="cv-btn-remove" onclick="this.closest(\'.cv-entry\').remove()">Remove</button></div>' +
        '<div class="cv-form-row">' +
        '<div class="cv-form-group"><label>Project Name</label><input type="text" name="projects[' + idx + '][name]" placeholder="Project name"></div>' +
        '<div class="cv-form-group"><label>Link</label><input type="text" name="projects[' + idx + '][link]" placeholder="https://..."></div>' +
        '</div>' +
        '<div class="cv-form-row full">' +
        '<div class="cv-form-group"><label>Technologies</label><input type="text" name="projects[' + idx + '][technologies]" placeholder="e.g. PHP, MySQL, JavaScript"></div>' +
        '</div>' +
        '<div class="cv-form-row full"><div class="cv-form-group"><label>Bullet Points</label>' +
        '<div class="cv-bullets-container"></div>' +
        '<button type="button" class="cv-btn-add-bullet" onclick="addBullet(this, \'projects\', ' + idx + ')">+ Add Bullet</button>' +
        '</div></div>';

    container.appendChild(entry);
}

function addSkill() {
    var container = document.getElementById('skills-entries');
    var idx = container.children.length;

    var entry = document.createElement('div');
    entry.className = 'cv-entry';
    entry.innerHTML =
        '<div class="cv-entry-header"><span>Skill Group #' + (idx + 1) + '</span>' +
        '<button type="button" class="cv-btn-remove" onclick="this.closest(\'.cv-entry\').remove()">Remove</button></div>' +
        '<div class="cv-form-row">' +
        '<div class="cv-form-group"><label>Category</label><input type="text" name="skills[' + idx + '][category]" placeholder="e.g. Languages, Frameworks"></div>' +
        '<div class="cv-form-group"><label>Skills (comma separated)</label><input type="text" name="skills[' + idx + '][items]" placeholder="e.g. Python, Java, C++"></div>' +
        '</div>';

    container.appendChild(entry);
}

function addCertificate() {
    var container = document.getElementById('certificate-entries');
    var idx = container.children.length;

    var entry = document.createElement('div');
    entry.className = 'cv-entry';
    entry.innerHTML =
        '<div class="cv-entry-header"><span>Certificate #' + (idx + 1) + '</span>' +
        '<button type="button" class="cv-btn-remove" onclick="this.closest(\'.cv-entry\').remove()">Remove</button></div>' +
        '<div class="cv-form-row">' +
        '<div class="cv-form-group"><label>Certificate Name</label><input type="text" name="certificates[' + idx + '][name]" placeholder="Certificate name"></div>' +
        '<div class="cv-form-group"><label>Issuer</label><input type="text" name="certificates[' + idx + '][issuer]" placeholder="e.g. Coursera, AWS"></div>' +
        '</div>' +
        '<div class="cv-form-row">' +
        '<div class="cv-form-group"><label>Date</label><input type="text" name="certificates[' + idx + '][date]" placeholder="e.g. Mar 2024"></div>' +
        '<div class="cv-form-group"><label>Link</label><input type="text" name="certificates[' + idx + '][link]" placeholder="https://..."></div>' +
        '</div>';

    container.appendChild(entry);
}

function addAward() {
    var container = document.getElementById('award-entries');
    var idx = container.children.length;

    var entry = document.createElement('div');
    entry.className = 'cv-entry';
    entry.innerHTML =
        '<div class="cv-entry-header"><span>Award #' + (idx + 1) + '</span>' +
        '<button type="button" class="cv-btn-remove" onclick="this.closest(\'.cv-entry\').remove()">Remove</button></div>' +
        '<div class="cv-form-row">' +
        '<div class="cv-form-group"><label>Award Name</label><input type="text" name="awards[' + idx + '][name]" placeholder="Award name"></div>' +
        '<div class="cv-form-group"><label>Date</label><input type="text" name="awards[' + idx + '][date]" placeholder="e.g. 2024"></div>' +
        '</div>' +
        '<div class="cv-form-row full">' +
        '<div class="cv-form-group"><label>Description</label><input type="text" name="awards[' + idx + '][description]" placeholder="Short description"></div>' +
        '</div>';

    container.appendChild(entry);
}

function addLanguage() {
    var container = document.getElementById('language-entries');
    var idx = container.children.length;

    var entry = document.createElement('div');
    entry.className = 'cv-entry';
    entry.innerHTML =
        '<div class="cv-entry-header"><span>Language #' + (idx + 1) + '</span>' +
        '<button type="button" class="cv-btn-remove" onclick="this.closest(\'.cv-entry\').remove()">Remove</button></div>' +
        '<div class="cv-form-row">' +
        '<div class="cv-form-group"><label>Language</label><input type="text" name="languages[' + idx + '][name]" placeholder="e.g. English"></div>' +
        '<div class="cv-form-group"><label>Level</label>' +
        '<select name="languages[' + idx + '][level]">' +
        '<option value="">Select level</option>' +
        '<option value="Native">Native</option>' +
        '<option value="Fluent">Fluent</option>' +
        '<option value="Advanced">Advanced</option>' +
        '<option value="Intermediate">Intermediate</option>' +
        '<option value="Beginner">Beginner</option>' +
        '</select></div>' +
        '</div>';

    container.appendChild(entry);
}

function toggleEndDate(checkbox) {
    var row = checkbox.closest('.cv-form-row');
    var endDateInput = row.querySelector('input[name*="end_date"]');
    if (!endDateInput) {
        var nextRow = row.nextElementSibling;
        if (nextRow) endDateInput = nextRow.querySelector('input[name*="end_date"]');
    }
    if (endDateInput) {
        endDateInput.disabled = checkbox.checked;
        if (checkbox.checked) {
            endDateInput.value = '';
            endDateInput.placeholder = 'Present';
        } else {
            endDateInput.placeholder = 'e.g. Dec 2023';
        }
    }
}

function validateCvForm() {
    var fullName = document.querySelector('input[name="full_name"]');
    var email = document.querySelector('input[name="email"]');
    var errors = [];

    if (!fullName || fullName.value.trim().length < 2) {
        errors.push('Full name is required (at least 2 characters).');
    }
    if (fullName && fullName.value.trim().length > 100) {
        errors.push('Full name is too long (max 100 characters).');
    }
    if (!email || email.value.trim() === '') {
        errors.push('Email is required.');
    } else {
        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email.value.trim())) {
            errors.push('Please enter a valid email address.');
        }
    }

    if (errors.length > 0) {
        alert(errors.join('\n'));
        return false;
    }
    return true;
}
