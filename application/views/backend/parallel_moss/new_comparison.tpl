{extends file='layouts/backend.tpl'}
{block title}{translate line='admin_parallel_moss_page_title'}{/block}
{block main_content}
    <h2>{translate line='admin_parallel_moss_page_title'}</h2>
    <span class="switches">
        <a href="{internal_url url='admin_parallel_moss/index'}">
            <i class="fa fa-arrow-right" aria-hidden="true"></i>
            {translate line='admin_parallel_moss_switch_to_index_page'}
        </a>
    </span>
    {include file='partials/backend_general/flash_messages.tpl' inline}

    <form action="" method="post">
        <div
            id="main_form_id"
            data-courses_url="{internal_url url='admin_parallel_moss/get_courses'}"
            data-task_sets_url="{internal_url url='admin_parallel_moss/get_task_sets'}"
            data-solutions_url="{internal_url url='admin_parallel_moss/get_solutions'}"
            data-settings_url="{internal_url url='admin_parallel_moss/get_settings'}"
            data-lang_add_new_comparison_button_text="{translate|escape:'html' line='admin_parallel_moss_add_new_comparison_button_text'}"
            data-lang_add_new_comparison_button_title="{translate|escape:'html' line='admin_parallel_moss_add_new_comparison_button_title'}"
            data-lang_remove_comparison_title="{translate|escape:'html' line='admin_parallel_moss_remove_comparison_title'}"
            data-lang_form_course_label="{translate|escape:'html' line='admin_parallel_moss_form_course_label'}"
            data-lang_form_task_set_label="{translate|escape:'html' line='admin_parallel_moss_form_task_set_label'}"
            data-lang_task_set_content_task_sets="{translate|escape:'html' line='admin_parallel_moss_task_set_content_task_sets'}"
            data-lang_task_set_content_projects="{translate|escape:'html' line='admin_parallel_moss_task_set_content_projects'}"
            data-lang_task_set_solutions_are_empty="{translate|escape:'html' line='admin_parallel_moss_task_set_solutions_are_empty'}"
        ></div>
    </form>
{/block}
