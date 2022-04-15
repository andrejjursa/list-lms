jQuery(document).ready(function($) {

    const construct_form = function () {
        let courses = 0;

        const mainForm = $('#main_form_id');
        mainForm.html('');

        const submitButton = $('<input>');

        const submitValidation = function () {
            submitButton.attr('disabled', 'disabled');

            const language = mainForm.find('select[name="moss_setup[l]"]');

            let valid = ($(language).val() !== '');

            const sensitivity = mainForm.find('input[name="moss_setup[m]"]');

            valid = valid && (
                $(sensitivity).val() !== '' && !isNaN($(sensitivity).val()) && parseInt($(sensitivity).val()) >= 0
            );

            const numberOfResults = mainForm.find('input[name="moss_setup[n]"]');

            valid = valid && (
                $(numberOfResults).val() !== '' && !isNaN($(numberOfResults).val()) && parseInt($(numberOfResults).val()) > 0
            );

            let selected = 0;

            const comparisonCheckboxes = mainForm.find('input[type=checkbox][name^=comparison]');

            for (let i = 0; i < comparisonCheckboxes.length; i++) {
                if (!$(comparisonCheckboxes.get(i)).is(':checked')) {
                    continue;
                }
                const name = $(comparisonCheckboxes.get(i)).attr('name');
                const versionSelect = name.replace('selected', 'version');

                const version = mainForm.find('select[name="' + versionSelect + '"]');
                if (version.length === 1 && $(version).val() !== '' && parseInt($(version).val()) > 0) {
                    selected++;
                }
            }

            valid = valid && (selected >= 2);

            if (valid) {
                submitButton.removeAttr('disabled');
            }
        };

        const resolve_task_set_content_type_name = function (content_type) {
            if (content_type === 'task_set') {
                return mainForm.attr('data-lang_task_set_content_task_sets');
            }
            return mainForm.attr('data-lang_task_set_content_projects');
        };

        const add_new_comparison = function () {
            ++courses;
            const comparisonBox = $('<div>');
            comparisonBox.addClass('comparison_box');
            comparisonContentBox.append(comparisonBox);

            const courseField = $('<div>');
            courseField.addClass('field');
            comparisonBox.append(courseField);

            const taskSetsField = $('<div>');
            taskSetsField.addClass('field');
            taskSetsField.addClass('hidden');
            comparisonBox.append(taskSetsField);

            const solutionsSelection = $('<div>');
            solutionsSelection.addClass('solutions-selection');
            solutionsSelection.addClass('hidden');
            comparisonBox.append(solutionsSelection);

            const baseFilesSelection = $('<div>');
            baseFilesSelection.addClass('base-files-selection');
            baseFilesSelection.addClass('hidden');
            comparisonBox.append(baseFilesSelection);

            const render_task_sets = function (selectedCourse) {
                const render_solutions_selection = function (selectedTaskSet) {
                    submitValidation();

                    baseFilesSelection.html('');
                    solutionsSelection.html('');
                    if (selectedTaskSet == 0) {
                        solutionsSelection.addClass('hidden');
                        baseFilesSelection.addClass('hidden');
                        return;
                    }
                    solutionsSelection.removeClass('hidden');

                    const solutionsList = $('<fieldset>');
                    solutionsList.addClass('solutions-list');
                    solutionsSelection.append(solutionsList);

                    const solutionsListLegend = $('<legend>');
                    solutionsListLegend.text(mainForm.attr('data-lang_fieldset_legend_solutions'));
                    solutionsList.append(solutionsListLegend);

                    const baseFilesList = $('<fieldset>');
                    baseFilesList.addClass('base-files-list');
                    baseFilesSelection.append(baseFilesList);

                    const baseFilesListLegend = $('<legend>');
                    baseFilesListLegend.text(mainForm.attr('data-lang_fieldset_legend_base_files'));
                    baseFilesList.append(baseFilesListLegend);

                    const solutionsUrl = mainForm.attr('data-solutions_url') + '/' + selectedTaskSet;
                    api_ajax_load_json(solutionsUrl, 'post', {}, function (data) {
                        const solutions = data.data.solutions;

                        if (solutions.length === 0) {
                            const errorDiv = $('<div>');
                            errorDiv.addClass('flash_message').addClass('message_error');
                            errorDiv.text(mainForm.attr('data-lang_task_set_solutions_are_empty'));
                            solutionsList.replaceWith(errorDiv);
                            baseFilesSelection.addClass('hidden');

                            submitValidation();

                            return;
                        }

                        for (let i in solutions) {
                            const solution = solutions[i];

                            const solutionBox = $('<div>');
                            solutionBox.addClass('solution_box');
                            solutionBox.addClass('field');
                            solutionsList.append(solutionBox);

                            const solutionLabel = $('<label>');
                            solutionBox.append(solutionLabel);

                            const solutionSelector = $('<input>');
                            solutionSelector.attr('type', 'checkbox');
                            solutionSelector.attr('value', 1);
                            solutionSelector.attr('checked', 'checked');
                            solutionSelector.attr('name', 'comparison[' + courses + '][solution][' + solution.id + '][selected]');
                            solutionSelector.change(submitValidation);
                            solutionLabel.append(solutionSelector);

                            const solutionStudent = $('<span>');
                            solutionStudent.text(solution.student.fullname);
                            solutionLabel.append(solutionStudent);

                            const solutionVersionWrap = $('<p>');
                            solutionVersionWrap.addClass('input');
                            solutionBox.append(solutionVersionWrap);

                            const solutionVersion = $('<select>');
                            solutionVersion.attr('size', '1');
                            solutionVersion.attr('name', 'comparison[' + courses + '][solution][' + solution.id + '][version]');
                            solutionVersionWrap.append(solutionVersion);

                            const versions = solution.versions;

                            let selectVersion = 0;
                            for (let v in versions) {
                                const version = versions[v];

                                const option = $('<option>');
                                option.attr('value', version.version);
                                option.text((version.version === solution.best_version ? '* ' : '') + version.version);
                                solutionVersion.append(option);
                                selectVersion = Math.max(selectVersion, version.version);
                            }
                            if (solution.best_version !== null) {
                                selectVersion = solution.best_version;
                            }
                            solutionVersion.val(selectVersion);

                        }

                        const baseFiles = data.data.base_files;

                        if (baseFiles.length > 0) {
                            baseFilesSelection.removeClass('hidden');
                        }

                        for (let i in baseFiles) {
                            const baseFile = baseFiles[i];

                            const baseFileTaskDiv = $('<div>');
                            baseFileTaskDiv.addClass('base-files-for-task');
                            baseFilesList.append(baseFileTaskDiv);

                            const baseFileTaskText = $('<div>');
                            baseFileTaskText.addClass('base-files-task-name');
                            baseFileTaskText.text(baseFile.task_name + ':');
                            baseFileTaskDiv.append(baseFileTaskText);

                            const baseFilesCheckboxes = $('<div>');
                            baseFilesCheckboxes.addClass('base-files-checkboxes');
                            baseFileTaskDiv.append(baseFilesCheckboxes);

                            let filesCounter = 0;

                            for (let filepath in baseFile.files) {
                                const filename = baseFile.files[filepath];

                                const baseFileLabel = $('<label>');
                                baseFilesCheckboxes.append(baseFileLabel);

                                const baseFileCheckbox = $('<input>');
                                baseFileCheckbox.attr('name', 'comparison[' + courses + '][baseFile][' + baseFile.task_id + '][' + (++filesCounter) + ']');

                                baseFileCheckbox.attr('type', 'checkbox');
                                baseFileCheckbox.attr('value', filepath);
                                baseFileLabel.append(baseFileCheckbox);

                                const baseFileCheckboxText = $('<span>');
                                baseFileCheckboxText.text(filename);
                                baseFileLabel.append(baseFileCheckboxText);
                            }
                        }

                        submitValidation();
                    });
                };

                taskSetsField.html('');
                if (selectedCourse == 0) {
                    taskSetsField.addClass('hidden');
                    render_solutions_selection(0);
                    return;
                }
                taskSetsField.removeClass('hidden');

                const taskSetsFieldLabel = $('<label>');
                taskSetsFieldLabel.attr('for', 'task_set_name_' + courses + '_id');
                taskSetsFieldLabel.addClass('required');
                taskSetsFieldLabel.text(mainForm.attr('data-lang_form_task_set_label') + ':');
                taskSetsField.append(taskSetsFieldLabel);

                const taskSetsInputWrap = $('<p>');
                taskSetsInputWrap.addClass('input');
                taskSetsField.append(taskSetsInputWrap);

                const taskSetsInput = $('<select>');
                taskSetsInput.attr('size', '1');
                taskSetsInput.attr('name', 'comparison[' + courses + '][task_set_id]');
                taskSetsInput.attr('id', 'task_set_name_' + courses + '_id');
                taskSetsInput.attr('disabled', 'disabled');
                taskSetsInput.on('change', function (event) {
                    render_solutions_selection(event.target.value);
                });
                taskSetsInputWrap.append(taskSetsInput);

                const taskSetsUrl = mainForm.attr('data-task_sets_url') + '/' + selectedCourse;
                api_ajax_load_json(taskSetsUrl, 'post', {}, function (data) {
                    taskSetsInput.html('');
                    taskSetsInput.removeAttr('disabled');

                    const emptyOption = $('<option>');
                    emptyOption.attr('value', 0);
                    taskSetsInput.append(emptyOption);

                    const contentTypes = data.data;
                    for (let contentType in contentTypes) {
                        const taskSets = contentTypes[contentType];
                        if (taskSets.length === 0) {
                            continue;
                        }
                        const optionGroup = $('<optgroup>');
                        optionGroup.attr('label', resolve_task_set_content_type_name(contentType));
                        taskSetsInput.append(optionGroup);
                        for (let i in taskSets) {
                            const taskSet = taskSets[i];

                            const option = $('<option>');
                            option.attr('value', taskSet.id);
                            option.text(taskSet.name);
                            optionGroup.append(option);
                        }
                    }
                });
            };

            const courseFieldLabel = $('<label>');
            courseFieldLabel.attr('for', 'course_name_' + courses + '_id');
            courseFieldLabel.addClass('required');
            courseFieldLabel.text(mainForm.attr('data-lang_form_course_label') + ':');
            courseField.append(courseFieldLabel);
            const courseFieldInputWrap = $('<p>');
            courseFieldInputWrap.addClass('input');
            courseField.append(courseFieldInputWrap);
            const courseFieldInput = $('<select>');
            courseFieldInput.attr('size', '1');
            courseFieldInput.attr('name', 'comparison[' + courses + '][course_id]');
            courseFieldInput.attr('id', 'course_name_' + courses + '_id');
            courseFieldInput.attr('disabled', 'disabled');
            courseFieldInput.on('change', function (event) {
                render_task_sets(event.target.value);
            });
            courseFieldInputWrap.append(courseFieldInput);

            const courses_url = mainForm.attr('data-courses_url');
            api_ajax_load_json(courses_url, 'post', {}, function (data) {
                courseFieldInput.removeAttr('disabled');
                courseFieldInput.html('');

                const emptyOption = $('<option>');
                emptyOption.attr('value', '0');
                courseFieldInput.append(emptyOption);

                const periods = data.data;

                for (let period in periods) {
                    const courses = periods[period];

                    const optionGroup = $('<optgroup>');
                    optionGroup.attr('label', period);
                    courseFieldInput.append(optionGroup);

                    for (let i in courses) {
                        const course = courses[i];
                        const option = $('<option>');
                        option.attr('value', course.id);
                        option.text(course.name);
                        optionGroup.append(option);
                    }
                }
            });

            const closeButtonBox = $('<div>');
            closeButtonBox.addClass('delete_box');
            const closeButton = $('<a>');
            closeButton.attr('href', 'javascript:void(0)');
            closeButton.html('<i class="fa fa-times" aria-hidden="true"></i>');
            closeButton.attr('title', mainForm.attr('data-lang_remove_comparison_title'));
            closeButton.on('click', function () {
                comparisonBox.remove();
                submitValidation();
            });
            closeButtonBox.append(closeButton);

            comparisonBox.append(closeButtonBox);
        };

        const addToComparisonButton = $('<a>');
        addToComparisonButton.text(mainForm.attr('data-lang_add_new_comparison_button_text'));
        addToComparisonButton.attr('title', mainForm.attr('data-lang_add_new_comparison_button_title'));
        addToComparisonButton.attr('href', 'javascript:void(0)');
        addToComparisonButton.addClass('button');
        addToComparisonButton.on('click', add_new_comparison);

        mainForm.append(addToComparisonButton);

        const comparisonContentBox = $('<div>');
        comparisonContentBox.addClass('comparison_content_box');
        mainForm.append(comparisonContentBox);

        const comparisonSettingsBox = $('<div>');
        comparisonSettingsBox.addClass('comparison_settings_box');
        mainForm.append(comparisonSettingsBox);

        const languageField = $('<div>');
        languageField.addClass('field');
        comparisonSettingsBox.append(languageField);

        const languageLabel = $('<label>');
        languageLabel.addClass('required');
        languageLabel.text(mainForm.attr('data-lang_form_language_label') + ':');
        languageField.append(languageLabel);

        const languageInputWrap = $('<div>');
        languageInputWrap.addClass('input');
        languageField.append(languageInputWrap);

        const languageSelect = $('<select>');
        languageSelect.attr('size', '1');
        languageSelect.attr('name', 'moss_setup[l]');
        languageSelect.change(submitValidation);
        languageInputWrap.append(languageSelect);

        const languageEmptyOption = $('<option>');
        languageEmptyOption.attr('value', '');
        languageEmptyOption.text('');
        languageSelect.append(languageEmptyOption);

        const url = mainForm.attr('data-settings_url');
        api_ajax_load_json(url, 'post', {}, function (data) {
            for (let lang in data.data.languages) {
                const langName = data.data.languages[lang];

                const option = $('<option>');
                option.attr('value', lang);
                option.text(langName);
                languageSelect.append(option);
            }
        });

        const sensitivityField = $('<div>');
        sensitivityField.addClass('field');
        comparisonSettingsBox.append(sensitivityField);

        const sensitivityLabel = $('<label>');
        sensitivityLabel.addClass('required');
        sensitivityLabel.text(mainForm.attr('data-lang_form_sensitivity_label') + ':');
        sensitivityField.append(sensitivityLabel);

        const sensitivityInputWrap = $('<div>');
        sensitivityInputWrap.addClass('input');
        sensitivityField.append(sensitivityInputWrap);

        const sensitivityInput = $('<input>');
        sensitivityInput.attr('type', 'number');
        sensitivityInput.attr('step', '1');
        sensitivityInput.attr('value', '10');
        sensitivityInput.attr('min', '0');
        sensitivityInput.attr('name', 'moss_setup[m]');
        sensitivityInput.change(submitValidation);
        sensitivityInputWrap.append(sensitivityInput);

        const sensitivityHint = $('<p>');
        sensitivityHint.addClass('input');
        sensitivityField.append(sensitivityHint);

        const sensitivityHintEm = $('<em>');
        sensitivityHintEm.text(mainForm.attr('data-lang_form_sensitivity_hint'));
        sensitivityHint.append(sensitivityHintEm);

        const comparisonControlBox = $('<div>');
        comparisonControlBox.addClass('comparison_control_box');
        mainForm.append(comparisonControlBox);

        const numberOfResultsField = $('<div>');
        numberOfResultsField.addClass('field');
        comparisonSettingsBox.append(numberOfResultsField);

        const numberOfResultsLabel = $('<label>');
        numberOfResultsLabel.addClass('required');
        numberOfResultsLabel.text(mainForm.attr('data-lang_form_number_of_results_label') + ':');
        numberOfResultsField.append(numberOfResultsLabel);

        const numberOfResultsInputWrap = $('<div>');
        numberOfResultsInputWrap.addClass('input');
        numberOfResultsField.append(numberOfResultsInputWrap);

        const numberOfResultsInput = $('<input>');
        numberOfResultsInput.attr('type', 'number');
        numberOfResultsInput.attr('step', '1');
        numberOfResultsInput.attr('min', '1');
        numberOfResultsInput.attr('value', '250');
        numberOfResultsInput.attr('name', 'moss_setup[n]');
        numberOfResultsInput.change(submitValidation);
        numberOfResultsInputWrap.append(numberOfResultsInput);

        const numberOfResultsHint = $('<p>');
        numberOfResultsHint.addClass('input');
        numberOfResultsField.append(numberOfResultsHint);

        const numberOfResultsHintEm = $('<em>');
        numberOfResultsHintEm.text(mainForm.attr('data-lang_form_number_of_results_hint'));
        numberOfResultsHint.append(numberOfResultsHintEm);

        submitButton.attr('type', 'submit');
        submitButton.attr('disabled', 'disabled');
        submitButton.addClass('button');
        submitButton.text('Submit');
        comparisonControlBox.append(submitButton);
    };

    construct_form();

});