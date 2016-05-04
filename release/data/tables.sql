create database if not exists app_aylein;

use app_aylein;

drop table if exists Documents;
drop table if exists Stages;
drop table if exists Types;
drop table if exists Action;
drop table if exists Main;
drop table if exists Comments;
drop table if exists SignOn;
drop table if exists Signs;
drop table if exists Users;

create table if not exists Users(
    userid int primary key auto_increment,
    username nvarchar(25) unique not null, #sYs#tsdfet.Aa
    userpass char(32) unique not null,
    userimg nvarchar(150),
    usertype char(5) default "visit", #visit guest admin
    usercreatetime datetime default CURRENT_TIMESTAMP,
    #userlastaction datetime, # default CURRENT_TIMESTAMP,
    usersort int default 0,
    uservalid int default 1
)auto_increment = 10086 charset = utf8;

create index user_name on Users(username);
create index user_pass on Users(userpass);
    
insert into Users(username, userpass, userimg, usertype) values
    ("AyleinOter", "126bd23a4bda50db3491502c0e14ea9d", "/images/master.jpg", "admin");

create table if not exists Signs(
    signid int primary key auto_increment,
    signname nvarchar(20) not null,
    signcreatetime datetime default CURRENT_TIMESTAMP,
    userid int not null,
    foreign key(userid) references Users(userid) on delete cascade on update cascade,
    signsort int default 0,
    signvalid int default 1
)auto_increment = 1 charset = utf8;

create table if not exists SignOn(
    soid int primary key auto_increment,
    signid int not null,
    foreign key(signid) references Signs(signid) on delete cascade on update cascade,
    userid int not null,
    foreign key(userid) references Users(userid) on delete cascade on update cascade,
    sotype char(5) not null, #stage comme
    sotypeid int default 0,
    socreatetime datetime default CURRENT_TIMESTAMP,
    sosort int default 0,
    sovalid int default 1
)auto_increment = 1 charset = utf8;

create table if not exists Types(
    typeid int primary key auto_increment,
    typepid int default 0,
    typeshow int default 0,
    typename nvarchar(15) not null,
    typecreatetime datetime default CURRENT_TIMESTAMP,
    typesort int default 0,
    typevalid int default 1
)auto_increment = 12580 charset = utf8;

create index types_name on Types(typename);
create index types_pid on Types(typepid);

create table if not exists Stages(
    stgid int primary key auto_increment,
    stgpid int default 0,
    typeid int not null,
    foreign key(typeid) references Types(typeid) on delete cascade on update cascade,
    userid int not null,
    foreign key(userid) references Users(userid) on delete cascade on update cascade,
    stgtype char(5), #stage docum
    stgtitle nvarchar(15) unique not null,
    stgsubtitle nvarchar(50) default "",
    stgnum int default 0,
    stgview int default 0,
    stgcomnum int default 0,
    stgcreatetime datetime default CURRENT_TIMESTAMP,
    stgupdatetime datetime default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
    stgsort int default 0,
    stgvalid int default 1
)auto_increment = 12110 charset = utf8;

create index stag_title on Stages(stgtitle);
create index stag_subtitle on Stages(stgsubtitle);

create table if not exists Documents(
    docid int primary key auto_increment,
    stgid int not null,
    foreign key(stgid) references Stages(stgid) on delete cascade on update cascade,
    doccontent nvarchar(12000) not null,
    docsort int default 0,
    docvalid int default 1
)auto_increment = 12315 charset = utf8;

create table if not exists Comments(
    comid int primary key auto_increment,
    comtype char(5) not null, #stage commt
    comtypeid int default 0,
    compid int default 0,
    userid int not null,
    foreign key(userid) references Users(userid) on delete cascade on update cascade,
    repeatid int default 0,
    repeatname nvarchar(12) default "",
    comdate datetime default CURRENT_TIMESTAMP,
    comment nvarchar(450) not null,
    comsort int default 0,
    comvalid int default 1
)auto_increment = 12306 charset = utf8;

create index com_type on Comments(comtype);
create index com_typeid on Comments(comtypeid);
create index com_pid on Comments(compid);

create table if not exists Main(
    id int primary key auto_increment,
    _key nvarchar(15) unique not null,
    _value nvarchar(125)
)auto_increment = 1 charset = utf8;

insert into Main (_key, _value) values 
    ("name", "AyleinOter"),
    ("fullname", "The IVth AyleinOter"),
    ("sign", "What a loser");

create table if not exists Action(
    actid int primary key auto_increment,
    acttype nvarchar(15) not null,
    acttypeid int default 0,
    acttitle nvarchar(25) not null,
    actlink nvarchar(50),
    actdate datetime default CURRENT_TIMESTAMP,
    actvalid int default 1
)auto_increment = 1  charset = utf8;

drop function if exists allComments;
delimiter //
create function allComments (ids varchar(2000))
returns varchar(2000)
begin
	declare temp varchar(4000);
    declare sTemp varchar(4000);
    set temp = "$";
    set sTemp = cast(ids as char);
	while sTemp is not null do
		set temp = concat(temp,',',sTemp);
		select group_concat(compid) into sTemp from comments where find_in_set(comid, sTemp) > 0;
	end while;
    return temp;
end //