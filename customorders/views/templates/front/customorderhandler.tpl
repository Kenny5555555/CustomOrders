{* Main DIV *}
<div style="margin:0 auto; width:1000px; align:center;">
	{* SubDiv 1 - Add new Orders / Form element *}
	<div style="width:100%; border:1px solid black; padding:10px; margin:5px;">
		<form action="{$link->getModuleLink('customorders', 'customorderhandler')|escape:'html'}" method="post">
		<input type="hidden" name="addNewOrder" value="1" />
			<p>Add new order:</p>
			<p>User:
				<select name="select_user">
					<option value="">--- Select User ---</option>
					{foreach from=$users item=usr}
						<option value="{$usr['id_user']}">{$usr['name']}</option>
					{/foreach}
				</select>
			</p>
			<p>
				Products:
				<select name="select_products">
					<option value="">--- Select Product ---</option>
					{foreach from=$products item=prod}
						<option value="{$prod['id_prod']}">{$prod['prod_name']}</option>
					{/foreach}
				</select>
			</p>
			<p>
				Quantity:
				<input type="number" id="quantity" name="quantity" min="1" max="100">
			</p>
			<p>
				<input type="submit" name="addOrder" value="add" />
			</p>
		</form>
	</div>
	
	{* SubDiv 2 - Filter/Search order list *}
	<div style="width:100%; border:1px solid black; padding:10px; margin:5px;">
		<p>Search:</p>
		<select id="dateSelect" name="select_order" onchange="orderSearchFunction()">
			<option value="1">All time</option>
			<option value="2">Last 7 days</option>
			<option value="3">Today</option>
		</select>
		<input type="text" id="searchInput" onkeyup="orderSearchFunction()" placeholder="enter search term..." style="margin-left:50px;">
	</div>
	
	{* SubDiv 3 - Order list *}
	<div style="width:100%; border:1px solid black; padding:10px; margin:5px; ">
		<table style="width:100%;">
			<thead style="display:block; align:center;">
				<tr>
					<th>User</th>
					<th style="min-width: 160px;">Product</th>
					<th>Price</th>
					<th style="min-width: 160px;">Quantity</th>
					<th>Total</th>
					<th>Date</th>
					<th>Actions</th>
				</tr>
			<thead>
			<form action="{$link->getModuleLink('customorders', 'customorderhandler')|escape:'html'}" method="post">
			<tbody id="orderList" style="height:400px; overflow:scroll; display: block; align:center;">
				{foreach from=$orders item=order}
					<tr>
						<td>{$order['name']}</td>
						<td style="min-width: 160px;">
							{* Visible name and hidden select for Edit function *}
							<div id="poselect-{$order['id_order']}">{$order['prod_name']}</div>
							<select id="prodselect-{$order['id_order']}" name="select_products-{$order['id_order']}" hidden>
								<option value="">Select Product</option>
								{foreach from=$products item=prod}
									<option value="{$prod['id_prod']}">{$prod['prod_name']}</option>
								{/foreach}
							</select>
						</td>
						<td>{$order['prod_price']} €</td>
						<td style="min-width: 160px;">
							{* Visible quantity and hidden input for Edit function *}
							<div id="poqty-{$order['id_order']}">{$order['quantity']}</div>
							<input type="number" id="quantity-{$order['id_order']}" name="quantity-{$order['id_order']}" min="1" max="100" hidden>
						</td>
						<td>{$order['total_cost']} €</td>
						<td>{$order['dt']}</td>
						<td>
							{* EDIT button switches visibility of some elements *}
							<button type="button" id="poebutton-{$order['id_order']}" onclick="toggle_order(this.id)" >edit</button>
							{* Delete button visible / Change post "OK" button hidden *}
							<button type="submit"  id="podbutton-{$order['id_order']}" name="deleteOrder"  onclick="return confirm('Are you sure you want to delete this order?')" value="{$order['id_order']}">delete</button>
							<button type="submit"  id="ocokbutton-{$order['id_order']}" name="changeOrder"  onclick="return confirm('Are you sure you want to change this order?')" value="{$order['id_order']}" hidden>ok</button>
						</td>
					</tr>
				{/foreach}
			</tbody>
			</form>
			</div>
		</table>
	</div>
	
</div>	

{literal}
  <style type="text/css">
	table, th, td {
	  border: 1px solid black;
	}
	th, td {
	 min-width: 110px;
	 max-width: 110px;
	 text-align:center;
	}
	tr > td:last-of-type {
		min-width: 150px;
	}
	tr > th:last-of-type {
		min-width: 150px;
	}
  </style>
{/literal}

<script type="text/javascript">
  {literal}
	function orderSearchFunction() { 
		// Declare variables and search terms
		var input, filter, table, list, srch1, srch2, i, f;
		input = document.getElementById('searchInput');
		filter = input.value.toUpperCase();
		var filters = filter.split(/\b(\s)/);
		table = document.getElementById("orderList");
		list = table.getElementsByTagName('tr');
		var select = document.getElementById("dateSelect");
		var selectedoption = select.options[select.selectedIndex].value;
		var srchdatetime, srchtimestamp, currdatestamp;

		// Loop through all list items, and hide those who don't match the search query
		for (i = 0; i < list.length; i++) {
		
			//First section filtration based on dateSelect selection
			var datecheck = 0;
			if(selectedoption == 1){
				datecheck = 1;
			}
			else if(selectedoption == 2){
				dt1 = new Date(srch1 = list[i].getElementsByTagName("td")[5].innerHTML);
				dt2 = new Date();
				var difference = Math.floor((Date.UTC(dt2.getFullYear(), dt2.getMonth(), dt2.getDate()) - Date.UTC(dt1.getFullYear(), dt1.getMonth(), dt1.getDate()) ) /(1000 * 60 * 60 * 24));
				if(difference < 7){
					datecheck = 1;
				}
			}
			else if(selectedoption == 3){
				srchdatetime = srch1 = list[i].getElementsByTagName("td")[5].innerHTML;
				srchtimestamp = new Date(srchdatetime).setHours(0, 0, 0, 0);
				currdatestamp = new Date().setHours(0, 0, 0, 0);
				if(srchtimestamp == currdatestamp){
					datecheck = 1;
				}
			}
			
			//Filtration based on search query
			var hit = 0;
			srch1 = list[i].getElementsByTagName("td")[0];
			srch2 = list[i].getElementsByTagName("td")[1].getElementsByTagName("div")[0];
			for (f = 0; f < filters.length; f++) {
				if (srch1.innerHTML.toUpperCase().indexOf(filters[f]) > -1 || srch2.innerHTML.toUpperCase().indexOf(filters[f]) > -1) {
					hit++;
				}
			}
			//Final visibility determined by both filtrations
			if(hit == filters.length && datecheck == 1){
				list[i].style.display = "";
			} else {
				list[i].style.display = "none";
			}
		}
	}
	
	function toggle_order(clicked_id)
	  {
		//Id from clicked button matches whole row
		ocid = clicked_id.replace("poebutton-", "");
		//All necessary fields toggled to opposite visibility
		document.getElementById("poselect-"+ocid).hidden = !document.getElementById("poselect-"+ocid).hidden;
		document.getElementById("poqty-"+ocid).hidden = !document.getElementById("poqty-"+ocid).hidden;
		document.getElementById("podbutton-"+ocid).hidden = !document.getElementById("podbutton-"+ocid).hidden;
		document.getElementById("prodselect-"+ocid).hidden = !document.getElementById("prodselect-"+ocid).hidden;
		document.getElementById("quantity-"+ocid).hidden = !document.getElementById("quantity-"+ocid).hidden;
		document.getElementById("ocokbutton-"+ocid).hidden = !document.getElementById("ocokbutton-"+ocid).hidden;
	  }
  {/literal}
</script>