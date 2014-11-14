use php;

create table if not exists Types (
    typeid int primary key auto_increment,
    typepid int default 0,
    typename nvarchar(15) not null,
    typesort int default 0,
    typevalid int default 1
)auto_increment = 12580;

create table if not exists Documents (
    docid int primary key auto_increment,
    typeid int references Types(typeid) on delete cascade on update cascade,
    docpid int default 0,
    doctitle nvarchar(15) not null,
    docsubtitle nvarchar(50) default "",
    doccontent nvarchar(12000) not null,
    docview int default 0,
    doccreatetime timestamp default current_timestamp,
    docupdatetime timestamp default current_timestamp on update current_timestamp,
    docsort int default 0,
    docvalid int default 1
)auto_increment = 12315;

create table if not exists Comments (
    comid int primary key auto_increment,
    docid int references Documents(docid) on delete cascade on update cascade,
    compid int default 0,
    comname nvarchar(12) not null,
    comdate timestamp default current_timestamp,
    comment nvarchar(450) not null,
    comsort int default 0,
    comvalid int default 1
)auto_increment = 12306;

create table if not exists Main ( 
    id int primary key auto_increment,
    key nvarchar(15) not null,
    value nvarchar(15)
)auto_increment = 1;

create table if not exists BBS (
    bbsid int primary key auto_increment,
    bbspid int default 0,
    bbsname nvarchar(12) not null,
    bbsre nvarchar(12),
    bbscontent nvarchar(450) not null,
    bbsdate timestamp default current_timestamp, 
    bbssort int default 0,
    bbsvalid int default 1
)auto_increment = 1;