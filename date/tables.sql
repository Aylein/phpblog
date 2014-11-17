use php;
drop table if exists Types;
create table if not exists Types (
    typeid int primary key auto_increment,
    typepid int default 0,
    typeshow int default 0,
    typename nvarchar(15) not null,
    typesort int default 0,
    typevalid int default 1
)auto_increment = 12580;
drop table if exists Documents;
create table if not exists Documents (
    docid int primary key auto_increment,
    typeid int references Types(typeid) on delete cascade on update cascade,
    docpid int default 0,
    doctitle nvarchar(15) not null,
    docsubtitle nvarchar(50) default "",
    docstgnum int default 0,
    doccomnum int default 0,
    docview int default 0,
    doccreatetime timestamp default current_timestamp,
    docupdatetime timestamp default current_timestamp on update current_timestamp,
    docsort int default 0,
    docvalid int default 1
)auto_increment = 12315;
drop table if exists Stage;
create table if not exists Stages (
    stgid int primary key auto_increment,
    typeid int references Types(typeid) on delete cascade on update cascade,
    docid int references Documents(docid) on delete cascade on update cascade,
    stgpid int default 0,
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
drop table if exists Comments;
create table if not exists Comments (
    comid int primary key auto_increment,
    comtype nvarchar(15) not null,
    comtypeid int not null,
    compid int default 0,
    comname nvarchar(12) not null,
    comdate timestamp default current_timestamp,
    comment nvarchar(450) not null,
    comsort int default 0,
    comvalid int default 1
)auto_increment = 12306;
drop table if exists Main;
create table if not exists Main ( 
    id int primary key auto_increment,
    _key nvarchar(15) not null,
    _value nvarchar(15)
)auto_increment = 1;
drop table if exists Action;
create table if not exists Action (
    actid int primary key auto_increment,
    acttype nvarchar(15) not null,
    acttitle nvarchar(25) not null,
    actlink nvarchar(50),
    actdate timestamp default current_timestamp
);