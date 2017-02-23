<a class="screenshotPopup" href="$Screenshot.Link">
    <% with Screenshot.SetWidth(250) %><img width="250" height="188" alt="$Title.ATT" src="$Link"/><% end_with %>
</a>
<div class="portFolioItem">

    <span class="notes">
        <% if NoLongerActive %>
            <a href="#" class="webPortfolioShowMore" rel="WebPortfolioItem$ID">This site</a> (for $Client) is no longer available.
        <% else %>
            <% if NotPubliclyAvailable %>
            <a href="#" class="webPortfolioShowMore" rel="WebPortfolioItem$ID">This site</a> (for $Client) is not publicly available.
            <% else %>
            <a href="#" class="webPortfolioShowMore" rel="WebPortfolioItem$ID">$WebAddress</a> (for $Client)
            <% end_if %>
        <% end_if %>
        <% if Notes %> - $Notes<% end_if %>
    </span>

    <div class="webPortfolioMoreInfo" id="WebPortfolioItem$ID">

    <% if Client %>
        <span class="client"><strong>Client:</strong> $Client</span>
    <% end_if %>

    <% if Design %>
        <span class="design"><strong>Design:</strong> $Design</span>
    <% end_if %>

    <% if CodingFrontEnd %>
        <span class="coding"><strong>Interaction coding:</strong> $CodingFrontEnd</span>
    <% end_if %>

    <% if CodingBackEnd %>
        <span class="coding"><strong>Functionality coding:</strong> $CodingBackEnd</span>
    <% end_if %>

    <% if Copy %>
        <span class="copy"><strong>Copy:</strong> $Copy</span>
    <% end_if %>

    <% if Photography %>
        <span class="photography"><strong>Photography:</strong> $Photography</span>
    <% end_if %>


    <% if WhatWeDid %>
        <span class="whatWeDid"><strong>Work completed:</strong>
            <% loop WhatWeDid %><a href="$Link"<% if Description %> title="$Description.ATT"<% end_if %>>$Name</a><% if Last %>.<% else %>, <% end_if %><% end_loop %>
        </span>
    <% end_if %>

    <% if Agent %>
        <span class="agent"><strong>Agency:</strong> <% with Agent %><a href="$AgentWebAddress.URL" class="externalLink">$Name</a><% end_with %></span>
    <% end_if %>

    <% if ScreenshotTaken %>
        <span class="screenshotTaken"><strong>Screenshot taken:</strong> $ScreenshotTaken.Year</span>
    <% end_if %>

    <span class="visit">
        <% if NoLongerActive %>
        <% else %>
            <% if NotPubliclyAvailable %>
            <% else %>
            <strong>Visit:</strong> <a href="$WebAddress.URL">$WebAddress.URL</a>
            <% end_if %>
        <% end_if %>
    </span>

    </div>

</div>
