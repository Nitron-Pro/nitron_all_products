{*
 * @author Mahdi Shad ( ramtin2025@yahoo.com )
 * @copyright Copyright iPresta.IR 2011-2018
 * @link iPresta.IR
*}

{if isset($no_follow) AND $no_follow}
	{assign var='no_follow_text' value='rel="nofollow"'}
{else}
	{assign var='no_follow_text' value=''}
{/if}

{if isset($p) AND $p}
	{assign var='requestPage' value=$current_url}
	<!-- Pagination -->
	<div id="pagination{if isset($paginationId)}_{$paginationId}{/if}" class="pagination clearfix">
		{if $start!=$stop}
			<ul class="pagination clearfix">
				{if $start==3}
					<li>
						<a type="button" {$no_follow_text}
                            onclick="$('#filter_page').val(1);$('#submit_filter').trigger('click');"
                            >
							1
						</a>
					</li>
					<li>
						<a type="button" {$no_follow_text}
                            onclick="$('#filter_page').val(2);$('#submit_filter').trigger('click');"
                            >
							2
						</a>
					</li>
				{/if}
				{if $start==2}
					<li>
						<a type="button" {$no_follow_text}
                            onclick="$('#filter_page').val(1);$('#submit_filter').trigger('click');"
                            >
							1
						</a>
					</li>
				{/if}
				{if $start>3}
					<li>
						<a type="button" {$no_follow_text}
                            onclick="$('#filter_page').val(1);$('#submit_filter').trigger('click');"
                            >
							1
						</a>
					</li>
					<li class="truncate">
						<span>
							...
						</span>
					</li>
				{/if}
				{section name=pagination start=$start loop=$stop+1 step=1}
					{if $p == $smarty.section.pagination.index}
						<li class="active current">
							<span>
								{$p|escape:'html':'UTF-8'}
							</span>
						</li>
					{else}
						<li>
    						<a type="button" {$no_follow_text}
                                onclick="$('#filter_page').val({$smarty.section.pagination.index});$('#submit_filter').trigger('click');"
                                >
    							{$smarty.section.pagination.index|escape:'html':'UTF-8'}
    						</a>
						</li>
					{/if}
				{/section}
				{if $pages_nb>$stop+2}
					<li class="truncate">
						<span>
							...
						</span>
					</li>
					<li>
						<a type="button" {$no_follow_text}
                            onclick="$('#filter_page').val({$pages_nb});$('#submit_filter').trigger('click');"
                            >
							{$pages_nb|intval}
						</a>
					</li>
				{/if}
				{if $pages_nb==$stop+1}
					<li>
						<a type="button" {$no_follow_text}
                            onclick="$('#filter_page').val({$pages_nb});$('#submit_filter').trigger('click');"
                            >
							{$pages_nb|intval}
						</a>
					</li>
				{/if}
				{if $pages_nb==$stop+2}
					<li>
						<a type="button" {$no_follow_text}
                            onclick="$('#filter_page').val({$pages_nb-1});$('#submit_filter').trigger('click');"
                            >
							{$pages_nb-1|intval}
						</a>
					</li>
					<li>
						<a type="button" {$no_follow_text}
                            onclick="$('#filter_page').val({$pages_nb});$('#submit_filter').trigger('click');"
                            >
							{$pages_nb|intval}
						</a>
					</li>
				{/if}
			</ul>
		{/if}
	</div>
	<!-- /Pagination -->
{/if}
