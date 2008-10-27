alter table PageStatistics add column date date null;
update PageStatistics set timestamp = timestamp, date = DATE_FORMAT(timestamp, '%Y-%m-%d');
alter table PageStatistics add index date (date);