{extends file='layouts/frontend_popup.tpl'}
{block title}{translate line='courses_detail_page_title'}{/block}
{block main_content}
    <h1>{translate line='courses_detail_page_title'}</h1>
    {if $course->exists()}
    <h2>{translate_text text=$course->name} / {translate_text text=$course->period_name}</h2>
    {/if}
    {include file='partials/frontend_general/flash_messages.tpl' inline}
    {if $course->exists()}
    <fieldset>
        <legend>{translate line='courses_detail_fieldset_legend_groups'}</legend>
        {$groups_contents = []}
        {foreach $course->group->order_by_with_constant('name', 'asc')->get_iterated() as $group}
            {capture name='assigned_group_capture' assign='assigned_group'}
            <div class="group_wrap">
                <div class="group_name">{translate_text text=$group->name}</div>
                <ul class="group_students">
                    {foreach $group->participant->where('allowed', 1)->include_related('student', '*', true, true)->order_by_related_as_fullname('student', 'fullname', 'asc')->get_iterated() as $participant}
                        <li>{$participant->student->fullname}</li>
                    {foreachelse}
                        <li>{translate line='courses_detail_group_empty'}</li>
                    {/foreach}
                </ul>
                <div class="group_teaching">{translate line='courses_detail_group_teaching'}:</div>
                <ul class="group_rooms">
                    {foreach $group->room->order_by('time_day', 'asc')->order_by('time_begin', 'asc')->get_iterated() as $room}
                        <li>{translate_text text=$room->name}: {$list_days[$room->time_day|intval]} ({$room->time_begin|is_time} - {$room->time_end|is_time})
                            <ul class="group_room_teachers">
                            {foreach $room->teachers->get_iterated() as $teacher}
                                <li>{$teacher->fullname}</li>
                            {/foreach}
                            {if $room->teachers_plain}
                                {$teachers_plain_names = ','|explode:$room->teachers_plain}
                                {foreach $teachers_plain_names as $teacher_plain_name}
                                <li>{$teacher_plain_name|trim}</li>
                                {/foreach}
                            {/if}
                            </ul>
                        </li>
                    {foreachelse}
                        <li>{translate line='courses_detail_group_without_rooms'}</li>
                    {/foreach}
                </ul>
            </div>
            {/capture}
            {$groups_contents[] = $assigned_group}
        {/foreach}
        {capture name='not_assigned_group_capture' assign='not_assigned_group'}
            <div class="group_wrap">
                <div class="group_name">{translate line='courses_detail_group_name_not_assigned'}</div>
                <ul class="group_students">
                    {foreach $course->participant->where('allowed', 1)->where('group_id', null)->include_related('student', '*', true, true)->order_by_related_as_fullname('student', 'fullname', 'asc')->get_iterated() as $participant}
                        <li>{$participant->student->fullname}</li>
                    {foreachelse}
                        <li>{translate line='courses_detail_group_empty'}</li>
                    {/foreach}
                </ul>
            </div>
        {/capture}
        {$groups_contents[] = $not_assigned_group}
        {foreach $groups_contents as $group_content}
            {$group_content}
            {if $group_content@last or $group_content@iteration is div by 3}<div class="clear"></div>{/if}
        {/foreach}
    </fieldset>
    {else}
        {include file='partials/frontend_general/error_box.tpl' message='lang:courses_course_not_found' inline}
    {/if}
{/block}