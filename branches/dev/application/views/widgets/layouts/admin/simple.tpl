<div class="widget simple_widget">
    <div class="widget_header">
        <h4>{block widget_name}{/block}</h4>
    </div>
    <div class="widget_content">
        {block widget_content}{/block}
    </div>
    <div class="widget_config">
        <a href="{internal_url url="admin_widget/configure/{$widget_id}"}" class="widget_config_link widget_id:{$widget_id}" title="{translate line='admin_widget_link_title_edit'}"><span class="list-icon list-icon-edit"></span></a>
        <a href="{internal_url url="admin_widget/delete/{$widget_id}"}" class="widget_delete_link widget_id:{$widget_id}" title="{translate line='admin_widget_link_title_delete'}"><span class="list-icon list-icon-delete"></span></a>
    </div>
</div>
<script type="text/javascript">
    {if $list_internal_css_files}
        {foreach $list_internal_css_files as $css_file}
            widget_styles.push('{$css_file.html|addslashes}');
        {/foreach}
    {/if}
</script>