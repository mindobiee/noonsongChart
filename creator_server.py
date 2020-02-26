import pymysql

conn = pymysql.connect(host = "127.0.0.1",
                       user = 'root', passwd = 'Song123~', db = 'gradproj', charset='utf8')
cur = conn.cursor()

query = "drop tables if exists musicList_bugs, comments_bugs"
cur.execute(query)

create_table_musicList_bugs = """
       create table musicList_bugs(
           id varchar(100),
           title varchar(100),
           artist varchar(100),
           album_title varchar(100),
           ranking INT,
           like_sum INT, 
           like_cnt INT,
           comments_sum INT,
           comments_cnt INT,
           img_url varchar(200),
           song_url varchar(200),
           primary key(ranking)
       )ENGINE=InnoDB DEFAULT CHARSET=utf8;
   """
cur.execute(create_table_musicList_bugs)

create_table_comments_bugs="""
    create table comments_bugs(
        id varchar(100),
        writerId varchar(100),
        comment varchar(500),
        time_of_crawl datetime default current_timestamp,
        primary key(id, writerId)
        )ENGINE=InnoDB DEFAULT CHARSET=utf8;
"""

cur.execute(create_table_comments_bugs)
conn.commit()
cur.close()
conn.close()
