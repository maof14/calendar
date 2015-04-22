# Calendar

## Swedish calendar

This is a calendar I made for a school project last year. I have a new idea for this calendar now so I thought I clean it up a bit and upload here. 

The calendar table adds the CSS weekend class 'weekend' to the Swedish public holidays, making them red together with the included CSS file. (As long as they are not Saturdays, in which case I don't care :).)

## Instructions for use

Create an instance of CCalendar: 
```php
	<?php 

	$calendar = new CCalendar();
```

Print the calendar table from the CCalendar object:
```php
	<?php

	echo $calendar->getCalendarTable();
```

Get the navigation (paginering): 
```php
	<?php

	echo $calendar->getNavigation();
```

Now you can click the links to navigate between years and months. 

Have fun!