{*
* @author Mahdi Shad ( ramtin2025@yahoo.com )
* @copyright Copyright Nitron.pro 2021-2023
* @link https://Nitron.pro
*}

{extends file="page.tpl"}
{block name='content'}
    <div class="panel nt-mb">
        <div class="panel-body">
            <div class="row">
                <form class="form" name="filter" value="1" id="filter">
                    <input type="hidden" name="filter_page" value="1" id="filter_page"/>
                    <div class="col-md-3 form-inline">
                        <div class="icon-wrap">
                            <i class="material-icons float-xs-right"></i>
                        </div>
                        <select name="category" id="category" class="chosen">
                            {foreach from=$categories item = "category"}
                                <option value="{$category.id_category}"
                                        {if $selected_category == $category.id_category}selected="selected"{/if}>{$category.name}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="col-md-1 form-inline">
                        <button name="submit_filter" id="submit_filter" value="1" type="submit" class="btn btn-primary">
                            {l s='Filter' d='Modules.Nitronallproducts.Front'}
                        </button>
                    </div>
                    <div class="col-md-1 form-inline">
                        <button type="button" class="btn btn-primary" onclick="exportProduct()">
                            {l s='Export' d='Modules.Nitronallproducts.Front'}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {if $products}
        <div class="table-responsive" id="block-products">
            <table class="table table-bordered">
                <thead class="thead-dark">
                <tr>
                    <th>{l s='Id' d='Modules.Nitronallproducts.Front'}</th>
                    {if isset($showImage) && $showImage && $showImage == 1 }
                        <th>{l s='Image' d='Modules.Nitronallproducts.Front'}</th>
                    {/if}
                    <th>{l s='Name' d='Modules.Nitronallproducts.Front'}</th>
                    <th>{l s='Price' d='Modules.Nitronallproducts.Front'} ({$currency.sign})</th>
                    {if isset($showQty) && $showQty && $showQty == 1 }
                        <th>{l s='Quantity' d='Modules.Nitronallproducts.Front'}</th>
                    {/if}
                    <th class="text-center">{l s='View' d='Modules.Nitronallproducts.Front'}</th>
                </tr>
                </thead>
                {foreach from=$products item=product key=key}
                    <tr>
                        <td>{$key}</td>
                        {if isset($showImage) && $showImage && $showImage == 1 }
                            <td><img width="40" height="40" src="{if $product.image}{$product.image}{else}{$shop.logo}{/if}" alt="{$product.name|escape:'html'}"/>
                            </td>
                        {/if}
                        <td><a class="text-info" href="{$product.link}">{$product.name|escape:'html'}</a></td>
                        <td>{$product.price|number_format:0}</td>
                        {if isset($showQty) && $showQty && $showQty == 1 }
                            <td>{$product.quantity}</td>
                        {/if}
                        <td class="text-info">
                            <a class="btn btn-info"
                               href="{$product.link}">{l s='Link' d='Modules.Nitronallproducts.Front'} </a>
                        </td>
                    </tr>
                {/foreach}
            </table>
        </div>
    {/if}
    {include file="./pagination.tpl"}
{/block}
