<div id="pages-controller-cms-content" class="cms-content center cms-tabset $BaseCSSClasses" data-layout-type="border" data-pjax-fragment="Content">

	<div class="cms-content-header north">
		<div class="cms-content-header-info">
			<% include CMSBreadcrumbs %>
		</div>
		<% if currentPageID %>
			<div class="cms-content-header-tabs">
				<ul class="cms-tabset-nav-primary">
					<li class="content-treeview<% if class == 'CMSPageEditController' %> ui-tabs-active ss-tabs-force-active<% end_if %>">
						<a href="$LinkPageEdit" class="cms-panel-link" title="Form_EditForm" data-href="$LinkPageEdit">
							<% _t('CMSMain.TabContent', 'Content') %>
						</a>
					</li>
				</ul>
			</div>
		<% else_if ManagedModels %>
			<div class="cms-content-header-tabs">
				<ul class="cms-tabset-nav-primary">
					<% loop ManagedModels %>
						<li class="content-listview<% if Current %> ui-tabs-active ss-tabs-force-active<% end_if %>">
							<a href="#cms-content-listview-$ParentID" class="cms-panel-link" data-href="$LinkListView">$Title</a>
						</li>
					<% end_loop %>
				</ul>
			</div>
		<% end_if %>
	</div>

	<% if currentPageID %>
		$EditForm
	<% else_if ManagedModels %>
		$Tools
		<div class="cms-content-fields center ui-widget-content cms-panel-padded">
			
			<% loop ManagedModels %>
				<div class="cms-content-view cms-panel-deferred" id="cms-content-listview-$ParentID" data-url="$LinkListView" data-deferred-no-cache="true">
					<%-- Lazy-loaded via ajax --%>
				</div>
			<% end_loop %>
			
		</div>
	<% end_if %>
	
</div>