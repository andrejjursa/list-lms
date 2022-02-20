jQuery(document).ready(function($) {

    var construct_form = function () {
        var courses = 0;

        var mainForm = $('#main_form_id');
        mainForm.html('');

        var resolve_task_set_content_type_name = function (content_type) {
            if (content_type === 'task_set') {
                return mainForm.attr('data-lang_task_set_content_task_sets');
            }
            return mainForm.attr('data-lang_task_set_content_projects');
        };

        var add_new_comparison = function () {
            ++courses;
            var comparisonBox = $('<div>');
            comparisonBox.addClass('comparison_box');
            comparisonContentBox.append(comparisonBox);

            var courseField = $('<div>');
            courseField.addClass('field');
            comparisonBox.append(courseField);

            var taskSetsField = $('<div>');
            taskSetsField.addClass('field');
            taskSetsField.addClass('hidden');
            comparisonBox.append(taskSetsField);

            var solutionsSelection = $('<div>');
            solutionsSelection.addClass('solutions-selection');
            solutionsSelection.addClass('hidden');
            comparisonBox.append(solutionsSelection);

            var render_task_sets = function (selectedCourse) {
                var render_solutions_selection = function (selectedTaskSet) {
                    solutionsSelection.html('');
                    if (selectedTaskSet == 0) {
                        solutionsSelection.addClass('hidden');
                        return;
                    }
                    solutionsSelection.removeClass('hidden');

                    var solutionsList = $('<fieldset>');
                    solutionsList.addClass('solutions-list');
                    solutionsSelection.append(solutionsList);

                    var solutionsUrl = mainForm.attr('data-solutions_url') + '/' + selectedTaskSet;
                    api_ajax_load_json(solutionsUrl, 'post', {}, function (data) {
                        var solutions = data.data.solutions;

                        for (var i in solutions) {
                            var solution = solutions[i];

                            var solutionBox = $('<div>');
                            solutionBox.addClass('solution_box');
                            solutionBox.addClass('field');
                            solutionsList.append(solutionBox);

                            var solutionLabel = $('<label>');
                            solutionBox.append(solutionLabel);

                            var solutionSelector = $('<input>');
                            solutionSelector.attr('type', 'checkbox');
                            solutionSelector.attr('value', 1);
                            solutionSelector.attr('checked', 'checked');
                            solutionSelector.attr('name', 'comparison[' + courses + '][solution][' + solution.id + '][selected]');
                            solutionLabel.append(solutionSelector);

                            var solutionStudent = $('<span>');
                            solutionStudent.text(solution.student.fullname);
                            solutionLabel.append(solutionStudent);

                            var solutionVersionWrap = $('<p>');
                            solutionVersionWrap.addClass('input');
                            solutionBox.append(solutionVersionWrap);

                            var solutionVersion = $('<select>');
                            solutionVersion.attr('size', '1');
                            solutionVersion.attr('name', 'comparison[' + courses + '][solution][' + solution.id + '][version]');
                            solutionVersionWrap.append(solutionVersion);

                            var versions = solution.versions;

                            var selectVersion = 0;
                            for (var v in versions) {
                                var version = versions[v];

                                var option = $('<option>');
                                option.attr('value', version.version);
                                option.text((version.version === solution.best_version ? '* ' :'') + version.version);
                                solutionVersion.append(option);
                                selectVersion = Math.max(selectVersion, version.version);
                            }
                            if (solution.best_version !== null) {
                                selectVersion = solution.best_version;
                            }
                            solutionVersion.val(selectVersion);

                        }
                    });
                }

                taskSetsField.html('');
                if (selectedCourse == 0) {
                    taskSetsField.addClass('hidden');
                    render_solutions_selection(0);
                    return;
                }
                taskSetsField.removeClass('hidden');

                var taskSetsFieldLabel = $('<label>');
                taskSetsFieldLabel.attr('for', 'task_set_name_' + courses + '_id');
                taskSetsFieldLabel.addClass('required');
                taskSetsFieldLabel.text(mainForm.attr('data-lang_form_task_set_label') + ':');
                taskSetsField.append(taskSetsFieldLabel);

                var taskSetsInputWrap = $('<p>');
                taskSetsInputWrap.addClass('input');
                taskSetsField.append(taskSetsInputWrap);

                var taskSetsInput = $('<select>');
                taskSetsInput.attr('size', '1');
                taskSetsInput.attr('name', 'comparison[' + courses + '][task_set_id]');
                taskSetsInput.attr('id', 'task_set_name_' + courses + '_id');
                taskSetsInput.attr('disabled', 'disabled');
                taskSetsInput.on('change', function (event) {
                    render_solutions_selection(event.target.value);
                });
                taskSetsInputWrap.append(taskSetsInput);

                var taskSetsUrl = mainForm.attr('data-task_sets_url') + '/' + selectedCourse;
                api_ajax_load_json(taskSetsUrl, 'post', {}, function (data) {
                    taskSetsInput.html('');
                    taskSetsInput.removeAttr('disabled');

                    var emptyOption = $('<option>');
                    emptyOption.attr('value', 0);
                    taskSetsInput.append(emptyOption);

                    var contentTypes = data.data;
                    for (var contentType in contentTypes) {
                        var taskSets = contentTypes[contentType];
                        if (taskSets.length === 0) {
                            continue;
                        }
                        var optionGroup = $('<optgroup>');
                        optionGroup.attr('label', resolve_task_set_content_type_name(contentType));
                        taskSetsInput.append(optionGroup);
                        for (var i in taskSets) {
                            var taskSet = taskSets[i];

                            var option = $('<option>');
                            option.attr('value', taskSet.id);
                            option.text(taskSet.name);
                            optionGroup.append(option);
                        }
                    }
                });
            };

            var courseFieldLabel = $('<label>');
            courseFieldLabel.attr('for', 'course_name_' + courses + '_id');
            courseFieldLabel.addClass('required');
            courseFieldLabel.text(mainForm.attr('data-lang_form_course_label') + ':');
            courseField.append(courseFieldLabel);
            var courseFieldInputWrap = $('<p>');
            courseFieldInputWrap.addClass('input');
            courseField.append(courseFieldInputWrap);
            var courseFieldInput = $('<select>');
            courseFieldInput.attr('size', '1');
            courseFieldInput.attr('name', 'comparison[' + courses + '][course_id]');
            courseFieldInput.attr('id', 'course_name_' + courses + '_id');
            courseFieldInput.attr('disabled', 'disabled');
            courseFieldInput.on('change', function (event) {
                render_task_sets(event.target.value);
            });
            courseFieldInputWrap.append(courseFieldInput);

            var courses_url = mainForm.attr('data-courses_url');
            api_ajax_load_json(courses_url, 'post', {}, function (data) {
                courseFieldInput.removeAttr('disabled');
                courseFieldInput.html('');

                var emptyOption = $('<option>');
                emptyOption.attr('value', '0');
                courseFieldInput.append(emptyOption);

                var periods = data.data;

                for (var period in periods) {
                    var courses = periods[period];

                    var optionGroup = $('<optgroup>');
                    optionGroup.attr('label', period);
                    courseFieldInput.append(optionGroup);

                    for (var i in courses) {
                        var course = courses[i];
                        var option = $('<option>');
                        option.attr('value', course.id);
                        option.text(course.name);
                        optionGroup.append(option);
                    }
                }
            });

            var closeButtonBox = $('<div>');
            closeButtonBox.addClass('delete_box');
            var closeButton = $('<a>');
            closeButton.attr('href', 'javascript:void(0)');
            closeButton.html('<i class="fa fa-times" aria-hidden="true"></i>');
            closeButton.attr('title', mainForm.attr('data-lang_remove_comparison_title'));
            closeButton.on('click', function () {
                comparisonBox.remove();
            });
            closeButtonBox.append(closeButton);

            comparisonBox.append(closeButtonBox);
        };

        var addToComparisonButton = $('<a>');
        addToComparisonButton.text(mainForm.attr('data-lang_add_new_comparison_button_text'));
        addToComparisonButton.attr('title', mainForm.attr('data-lang_add_new_comparison_button_title'));
        addToComparisonButton.attr('href', 'javascript:void(0)');
        addToComparisonButton.addClass('button');
        addToComparisonButton.on('click', add_new_comparison);

        mainForm.append(addToComparisonButton);

        var comparisonContentBox = $('<div>');
        comparisonContentBox.addClass('comparison_content_box');
        mainForm.append(comparisonContentBox);
    };

    construct_form();

});