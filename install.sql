
create table if not exists naju_event (
	event_id int(10) unsigned not null auto_increment,
	event_name varchar(75) not null,
	event_group int(10) unsigned not null,
	event_start date not null,

	event_end date,
	event_description mediumtext,

	event_location varchar(100),
	event_target_group varchar(100),
	event_price varchar(15),
	event_price_reduced varchar(15),
	event_registration varchar(75),

	event_target_group_type set('children', 'teens', 'families', 'young_adults'),
	event_type enum('camp', 'workshop', 'work_assignment', 'group_meeting', 'other'),

	event_link int(10) unsigned,
	event_active boolean not null default true,

	primary key (event_id),
	foreign key fk_event_group (event_group) references naju_local_group(group_id),
	foreign key fk_event_article (event_link) references rex_article(id)
);
