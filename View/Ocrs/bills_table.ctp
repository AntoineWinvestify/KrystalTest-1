<?php
/**
 * +---------------------------------------------------------------------------------------------+
 * | Copyright (C) 2017, http://www.winvestify.com                                               |
 * +---------------------------------------------------------------------------------------------+
 * | This file is free software; you can redistribute it and/or modify 				 |
 * | it under the terms of the GNU General Public License as published by  			 |
 * | the Free Software Foundation; either version 2 of the License, or                           |
 * | (at your option) any later version.                                      			 |
 * | This file is distributed in the hope that it will be useful   				 |
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of                          	 |
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the                                |
 * | GNU General Public License for more details.        			              	 |
 * +---------------------------------------------------------------------------------------------+
 *
 *
 * @author
 * @version 0.1
 * @date 2017-05-29
 * @package
 * 
 * 
 * [2017-05-29]
 * Create table
 */
echo $result;
if ($result) {
    ?>

    <table id="billsHistory" class="display dataTable"  width="100%" cellspacing="0" data-order='[[ 1, "asc" ]]' data-page-length='25' rowspan='1' colspan='1'>
        <tr>
            <th width="15%"><?php echo __('PFP') ?></th>
            <th><?php echo __('Date') ?></th>
            <th><?php echo __('Bill Number') ?></th>
            <th><?php echo __('Concept') ?></th>
            <th><?php echo __('Amount') ?></th>
        </tr>
        <?php foreach ($bills as $billsTable) {//Bills table creation   ?>
            <tr>
                <td><?php echo __($billsTable['Pfpname']) ?></td>
                <td><?php echo __($billsTable['info']['created']) ?></td>
                <td><?php echo __($billsTable['info']['bill_number']) ?></td>
                <td><?php echo __($billsTable['info']['bill_concept']) ?></td>
                <td align="left"><?php echo __($billsTable['info']['bill_amount']) ?></td>
            </tr>
        <?php } ?>
    </table>
    <?php
} else {
    echo $message;
}