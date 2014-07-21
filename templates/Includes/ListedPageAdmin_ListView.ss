<div class="cms-content-toolbar">
	<% include ListedPageAdmin_ContentToolActions %>
</div>

<div class="cms-panel-content center">
	<% if TreeIsFiltered %>
	<div class="cms-tree-filtered">
		<strong><% _t('CMSMain.ListFiltered', 'Filtered list.') %></strong>
		<a href="$LinkPages" class="cms-panel-link">
			<% _t('CMSMain.TreeFilteredClear', 'Clear filter') %>
		</a>
	</div>
	<% end_if %>

	<div class="cms-list" data-url-list="$Link(getListViewHTML)">
		$ListViewForm
	</div>
</div>