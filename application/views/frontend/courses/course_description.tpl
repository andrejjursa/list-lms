{extends file='layouts/frontend.tpl'}
{block title}{translate line='courses_page_description_title'}{/block}
{block main_content}
    <h1>{translate line='courses_page_description_title'}</h1>
    {include file='partials/frontend_general/flash_messages.tpl' inline}
    {if $course->exists()}
        {$description={overlay table='courses' table_id=$course->id column='description' default=$course->description}}
        {if $description}
            <fieldset class="content_fieldset">
                <legend>{translate line='courses_description_legend_description'}</legend>
                {$description}
            </fieldset>
        {/if}
        {$instructions={overlay table='courses' table_id=$course->id column='instructions' default=$course->instructions}}
        {if $instructions}
            <fieldset class="content_fieldset">
                <legend>{translate line='courses_description_legend_instructions'}</legend>
                {$instructions}
            </fieldset>
        {/if}
        {$syllabus={overlay table='courses' table_id=$course->id column='syllabus' default=$course->syllabus}}
        {if $syllabus}
            <fieldset class="content_fieldset">
                <legend>{translate line='courses_description_legend_syllabus'}</legend>
                {$syllabus}
            </fieldset>
        {/if}
        {$grading={overlay table='courses' table_id=$course->id column='grading' default=$course->grading}}
        {if $grading}
            <fieldset class="content_fieldset">
                <legend>{translate line='courses_description_legend_grading'}</legend>
                {$grading}
            </fieldset>
        {/if}
        {$other_texts={overlay table='courses' table_id=$course->id column='other_texts' default=$course->other_texts}}
        {if $other_texts}
            <fieldset class="content_fieldset">
                <legend>{translate line='courses_description_legend_other_texts'}</legend>
                {$other_texts}
            </fieldset>
        {/if}
    {else}
        {include file='partials/frontend_general/error_box.tpl' message='lang:courses_no_active_course' inline}
    {/if}
{/block}
{block custom_head}
    <script type="text/javascript">

    </script>
{/block}