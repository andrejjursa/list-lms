<div class="widget simple_widget">
    <div class="widget_header">
        <h4>{block widget_name}{/block}</h4>
    </div>
    <div class="widget_content">
        {block widget_content}{/block}
    </div>
    <div class="widget_config">
        <a href="{internal_url url="admin_widget/configure/{$widget_id}"}" class="widget_config_link widget_id:{$widget_id}">E</a>
        <a href="{internal_url url="admin_widget/delete/{$widget_id}"}" class="widget_delete_link widget_id:{$widget_id}">D</a>
    </div>
</div>