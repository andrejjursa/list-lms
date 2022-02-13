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
        <div id="main_form_id">

        </div>
    </form>
{/block}
