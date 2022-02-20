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

            var render_task_sets = function (selectedCourse) {
                taskSetsField.html('');
                if (selectedCourse == 0) {
                    taskSetsField.addClass('hidden');
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
                        console.log(contentType, taskSets);
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