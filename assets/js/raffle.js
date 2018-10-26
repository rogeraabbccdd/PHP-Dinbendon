// https://codepen.io/Re3ker/pen/VYBXBj
var items = [],
shuffled = [],
loadout = $("#loadout"),
insert_times = 30,
duration_time = 10000;
$("#roll").click(function () {
	items = [];
   $(".items").each(function(){
	   items.push($(this));
   })
   $("#roll").attr("disabled", true);
   var scrollsize = 0,
   diff = 0;
   $("#result").text("隨機...");
   $(loadout).html("");
   loadout.css("left", "100%");
   if (items.length < 10) {
	   insert_times = 2;
	   duration_time = 3000;
   } else {
	   insert_times = 1;
	   duration_time = 5000;
   }
   for (var times = 0; times < insert_times; times++) {
	   shuffled = items;
	   shuffled.shuffle();
	   for (var i = 0; i < items.length; i++) {
		    console.log( $(shuffled[i]).eq(0).find("img") );

		   loadout.append("<td class='items'><div class='roller'><img src='"+
					   $(shuffled[i]).eq(0).find("img").attr("src") +
					   "' width='100%' height='100%'><div><p>"+
					   $(shuffled[i]).eq(0).find("p").text() +
					   "<\/p><\/div><\/div><\/td>");
		   scrollsize = scrollsize + 192;
	   }
   }

   diff = Math.round(scrollsize / 2);
   diff = randomEx(diff - 300, diff + 300);
   $("#loadout").animate({
	   left: "-=" + diff },
   duration_time, function () {
	   $("#roll").attr("disabled", false);
	   $('#loadout').children('td').each(function () {
		   var center = window.innerWidth / 2;
		   if ($(this).offset().left < center && $(this).offset().left + 185 > center) {
			   var text = $(this).children().text();
			  $("#result").text("今天就吃 "+text+" 吧");
		   }

	   });
   });
});
Array.prototype.shuffle = function () {
   var counter = this.length,temp,index;
   while (counter > 0) {
	   index = Math.random() * counter-- | 0;
	   temp = this[counter];
	   this[counter] = this[index];
	   this[index] = temp;
   }
};
function randomEx(min, max)
{
   return Math.floor(Math.random() * (max - min + 1) + min);
}