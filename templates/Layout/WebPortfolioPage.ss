<% if FilterList %>
<div id="Sidebar">
    <div class="sidebarBox filterList">
        <ul>
            <li class="<% if HasFilter %>link<% else %>current<% end_if %>"><a href="$Link">favourites</a></li>
    <% loop FilterList %>
            <li class="$LinkingMode"><a href="$Link">$Name</a>
    <% if LinkingMode = current %>: <span>$Description</span><% end_if %>
            </li>
    <% end_loop %>
        </ul>
    </div>
</div>
<% end_if %>



<div id="MainContentSection">
    <h1 id="PageTitle">$Title</h1>
    <% if currentDescription %><p><em>$CurrentDescription</em></p><% end_if %>
    $Content
</div>
<% if SelectedWebPortfolioItems %>
<ul id="WebPortfolioItems">
<% loop SelectedWebPortfolioItems %>
    <li class="$EvenOdd $FirstLast">
        <% include WebPortfolioPageOneItem %>
    </li>
<% end_loop %>
</ul>
<% end_if %>
<% if HasFilter %><p class="returnToNormal"><a href="$Link">show all items in <i>$MenuTitle</i></a></p><% end_if %>
$Form
$PageComments
