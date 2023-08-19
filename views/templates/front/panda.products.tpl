{*
* 2007-2019 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2019 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{extends file="page.tpl"}
{block name='content'}
    <div class="panel nt-mb">
        <div class="panel-body">
            <div class="row">
                <form class="form" name="filter" value="1" id="filter">
                    <input type="hidden" name="filter_page" value="1" id="filter_page"/>
                    <div class="col-md-4 d-inline-block">
                        <div class="icon-wrap">
                            <i class="fto-angle-down arrow_down arrow"></i>
                        </div>
                        <select name="category" id="category" class="chosen">
                            {foreach from=$categories item = "category"}
                                <option value="{$category.id_category}"
                                        {if $selected_category == $category.id_category}selected="selected"{/if}>{$category.name}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="col-md-2 d-inline-block">
                        <button name="submit_filter" id="submit_filter" value="1" type="submit" class="btn btn-danger">
                            {l s='Filter' d='Modules.Nitronallproducts.Front'}
                        </button>
                        <button type="button" class="btn btn-success" onclick="exportProduct()">
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
                            <td><img width="40" height="40" src="{if isset($product.image) && $product.image}{$product.image}{else}test{/if}" alt="{$product.name|escape:'html'}"/>
                            </td>
                        {/if}
                        <td><a class="text-info" href="{$product.link}">{$product.name|escape:'html'}</a></td>
                        <td>{$product.price|number_format:0}</td>
                        {if isset($showQty) && $showQty && $showQty == 1 }
                            <td>{$product.quantity}</td>
                        {/if}
                        <td class="text-info">
                            <a class="btn btn-info btn-block"
                               href="{$product.link}">{l s='Link' d='Modules.Nitronallproducts.Front'} </a>
                        </td>
                    </tr>
                {/foreach}
            </table>
        </div>
    {/if}
    {include file="./pagination.tpl"}
{/block}
