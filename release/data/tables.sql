create database if not exists phpMyBlog;

use phpMyBlog;

drop table if exists Stages;
drop table if exists Documents;
drop table if exists Types;
drop table if exists Action;
drop table if exists Main;
drop table if exists Comments;
drop table if exists Users;

create table if not exists Users(
    userid int primary key auto_increment,
    username nvarchar(12) unique not null, # sYs#tsdfet.Aa
    userpass char(32) unique not null,
    userimg nvarchar(150),
    usertype char(5) default "visit", #visit admin
    usercreatetime timestamp default current_timestamp,
    usersort int default 0,
    uservalid int default 1
)auto_increment = 10086;

create index user_name on Users(username);
create index user_pass on Users(userpass);

insert into Users(username, userpass, userimg, usertype) values
    ("AyleinOter", "77abf970792365c929648aecc39d06fc", "/images/master.jpg", "admin");

create table if not exists Types(
    typeid int primary key auto_increment,
    typepid int default 0,
    typeshow int default 0,
    typename nvarchar(15) unique not null,
    typereatetime timestamp default current_timestamp,
    typesort int default 0,
    typevalid int default 1
)auto_increment = 12580;

create index types_name on Types(typename);
create index types_pid on Types(typepid);

create table if not exists Documents(
    docid int primary key auto_increment,
    typeid int not null,
    foreign key(typeid) references Types(typeid) on delete cascade on update cascade,
    doctitle nvarchar(15) not null,
    docsubtitle nvarchar(50) unique not null,
    docstgnum int default 0,
    doccomnum int default 0,
    docview int default 0,
    doccreatetime timestamp default current_timestamp,
    docupdatetime timestamp default current_timestamp on update current_timestamp,
    docsort int default 0,
    docvalid int default 1
)auto_increment = 12315;

create index doc_typeid on Documents(typeid);
create index doc_title on Documents(doctitle);

create table if not exists Stages(
    stgid int primary key auto_increment,
    docid int not null,
    foreign key (docid) references Documents(docid) on delete cascade on update cascade,
    stgtitle nvarchar(15) not null,
    stgsubtitle nvarchar(50) default "",
    stgcontent nvarchar(12000) not null,
    stgview int default 0,
    stgcomnum int default 0,
    stgcreatetime timestamp default current_timestamp,
    stgupdatetime timestamp default current_timestamp on update current_timestamp,
    stgsort int default 0,
    stgvalid int default 1
)auto_increment = 12110;

create index stag_docid on Stages(docid);
create index stag_title on Stages(stgtitle);
create index stag_subtitle on Stages(stgsubtitle);

create table if not exists Comments(
    comid int primary key auto_increment,
    comtype nvarchar(15) not null,
    comtypeid int default 0,
    compid int default 0,
    comname nvarchar(12) not null,
    comrename nvarchar(12) default "",
    comdate timestamp default current_timestamp,
    comment nvarchar(450) not null,
    comsort int default 0,
    comvalid int default 1
)auto_increment = 12306;

create index com_type on Comments(comtype);
create index com_typeid on Comments(comtypeid);
create index com_pid on Comments(compid);

create table if not exists Main(
    id int primary key auto_increment,
    _key nvarchar(15) unique not null,
    _value nvarchar(15)
)auto_increment = 1;

create table if not exists Action(
    actid int primary key auto_increment,
    acttype nvarchar(15) not null,
    acttypeid int default 0,
    acttitle nvarchar(25) not null,
    actlink nvarchar(50),
    actdate timestamp default current_timestamp,
    actvalid int default 1
);