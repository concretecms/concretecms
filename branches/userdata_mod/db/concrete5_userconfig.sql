alter table Config add column uID int unsigned not null default 0;
alter table Config drop primary key, add primary key (uID, cfKey);
alter table Config add index (uID);