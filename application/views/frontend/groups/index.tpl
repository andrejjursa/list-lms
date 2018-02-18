{extends file='layouts/frontend.tpl'}
{block title}{translate line='groups_page_title'}{/block}
{block main_content}
    <h1>{translate line='groups_page_title'}</h1>
    {if $course->exists()}
        <h2>{translate_text text=$course->name} / {translate_text text=$course->period_name}</h2>
    {/if}
    {include file='partials/frontend_general/flash_messages.tpl' inline}
    {if $course->exists()}
    <fieldset>
        <legend>{translate_text text=$course->name} / {translate_text text=$course->period->get()->name}</legend>
        {if !is_null($course->groups_change_deadline) and $can_change_group}
            <div class="change_deadline">{translate|sprintf:{$course->groups_change_deadline|date_format:{translate line='groups_datetime_format'}} line='groups_deadline_warning'}</div>
        {/if}
        <form action="{internal_url url='groups/select_group'}" method="post">
            {capture name='group_selection_submit' assign='group_selection_submit'}{if $can_change_group}
            <div class="select_group"><input type="submit" name="submit_button" value="{translate line='groups_submit_button'}" class="button" /></div>
            {/if}{/capture}
            {$group_selection_submit}
            <div class="groups_wrap">
                {$captured_groups = []}
                {foreach $course->group->order_by_with_constant('name', 'asc')->get_iterated() as $group}
                    {capture name='capture_group' assign='captured_group'}
                    <div class="group_wrap">
                        <div class="group_name">{if $can_change_group}<input type="radio" name="group_id" value="{$group->id}" /> {/if}{translate_text text=$group->name} ({$group->participant->where_related($course)->where('allowed', 1)->count()}&nbsp;/&nbsp;{$group->room->order_by('capacity', 'asc')->limit(1)->get()->capacity|intval})</div>
                        <ul class="group_students">
                        {foreach $group->participant->include_related('student', ['fullname','id'])->where_related($course)->where('allowed', 1)->order_by_related_as_fullname('student', 'fullname', 'asc')->get_iterated() as $participant}
                            <li{if $participant->student_id eq $list_student_account.id} class="me"{/if}>{$participant->student_fullname}</li>
                        {foreachelse}
                            <li>{translate line='groups_group_is_empty'}</li>
                        {/foreach}
                        </ul>
                        <div class="group_teaching">{translate line='groups_teaching'}:</div>
                        <ul class="group_rooms">
                        {foreach $group->room->order_by('time_day', 'asc')->order_by('time_begin', 'asc')->get_iterated() as $room}
                            <li>{translate_text text=$room->name}: {$list_days[$room->time_day]} {$room->time_begin|is_time} - {$room->time_end|is_time}</li>
                        {/foreach}
                        </ul>
                    </div>
                    {/capture}
                    {$captured_groups[] = $captured_group}
                {/foreach}
                {capture name='capture_group' assign='captured_group'}
                <div class="group_wrap">
                    <div class="group_name">{translate line='groups_not_assigned_group_name'}</div>
                    <ul class="group_students">
                    {foreach $course->participant->include_related('student', ['fullname', 'id'])->where('allowed', 1)->where('group_id', null)->order_by_related_as_fullname('student', 'fullname', 'asc')->get_iterated() as $participant}
                        <li{if $participant->student_id eq $list_student_account.id} class="me"{/if}>{$participant->student_fullname}</li>
                    {foreachelse}
                            <li>{translate line='groups_group_is_empty'}</li>
                    {/foreach}
                    </ul>
                </div>
                {/capture}
                {$captured_groups[] = $captured_group}
                {foreach $captured_groups as $captured_group}
                    {$captured_group}
                    {if $captured_group@last or $captured_group@iteration is div by 3}<div class="clear"></div>{/if}
                {/foreach}
            </div>
            {$group_selection_submit}
        </form>
    </fieldset>
    {else}
        {include file='partials/frontend_general/error_box.tpl' message='lang:groups_error_no_active_course' inline}
    {/if}
{/block}