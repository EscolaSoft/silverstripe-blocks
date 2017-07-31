<% if $isAdmin %>
  <% loop $ActiveBlocks %>
    <div class="block {$ClassName} editable">
      <div>$Me</div>
      <a href="{$EditLink}" class="editlink">Edit this page</a>
    </div>
  <% end_loop %>
<% else %>
  <% loop $ActiveBlocks %>
    <div class="block {$ClassName}">$Me</div>
  <% end_loop %>
<% end_if %>
