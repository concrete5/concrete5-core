!function(a,b){"use strict";function c(a){a.find("table[data-table=event-list] tbody tr").each(function(){b(this).concreteCalendarEventMenu({menu:b(this).find("div[data-event-occurrence]")})}),a.find("table.ccm-dashboard-calendar div.ccm-dashboard-calendar-date-event > a").each(function(){b(this).concreteCalendarEventMenu({menu:b(this).parent().find("div[data-event-occurrence]")})})}c.setupVersionsTable=function(a){a.on("click","input[name=eventVersionID]",function(){var c=b(this).val();c==-1?b.concreteAjax({url:CCM_DISPATCHER_FILENAME+"/ccm/calendar/event/version/unapprove_all",data:{eventID:b(this).data("event-id"),ccm_token:b(this).data("token")},success:function(c){ConcreteAlert.notify({message:c.message}),b("#ccm-calendar-event-version-reload").show(),a.find("tr[class=success]").removeClass(),a.find("a[data-action=delete-version]").show()}}):b.concreteAjax({url:CCM_DISPATCHER_FILENAME+"/ccm/calendar/event/version/approve",data:{eventVersionID:c,ccm_token:b(this).data("token")},success:function(d){ConcreteAlert.notify({message:d.message}),b("#ccm-calendar-event-version-reload").show(),a.find("tr[class=success]").removeClass(),a.find("a[data-action=delete-version]").show(),a.find("tr[data-calendar-event-version-id="+c+"]").addClass("success"),a.find("tr[data-calendar-event-version-id="+c+"] a[data-action=delete-version]").hide()}})}),a.on("click","a[data-action=delete-version]",function(){var c=b(this).attr("data-calendar-event-version-id");b.concreteAjax({url:CCM_DISPATCHER_FILENAME+"/ccm/calendar/event/version/delete",data:{eventVersionID:c,ccm_token:b(this).data("token")},success:function(d){ConcreteAlert.notify({message:d.message}),b("#ccm-calendar-event-version-reload").show();var e=a.find("tr[data-calendar-event-version-id="+c+"]");e.queue(function(){b(this).addClass("animated fadeOutDown"),b(this).dequeue()}).delay(500).queue(function(){b(this).remove(),b(this).dequeue()})}})})},a.ConcreteCalendarAdmin=c}(this,$),!function(a,b,c){"use strict";function d(a,c){var d=this,c=c||{};c=b.extend({container:!1},c),d.options=c,a&&ConcreteMenu.call(d,a,c)}d.prototype=Object.create(ConcreteMenu.prototype),d.prototype.setupMenuOptions=function(a){var b=this,c=ConcreteMenu.prototype;b.options.container;c.setupMenuOptions(a)},b.fn.concreteCalendarEventMenu=function(a){return b.each(b(this),function(c,e){new d(b(this),a)})},a.ConcreteCalendarEventMenu=d}(this,$,_);