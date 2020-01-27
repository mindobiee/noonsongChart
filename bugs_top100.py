from selenium import webdriver
from bs4 import BeautifulSoup
import pymysql

chromedriver_dir = r'C:\Users\user\Downloads\chromedriver_win32\chromedriver.exe'
driver = webdriver.Chrome(chromedriver_dir)

#create cursor
conn = pymysql.connect(host="127.0.0.1",
                       user ='root', passwd='960919', db="gradualDB" )
cur = conn.cursor()

# options to look like a human
options = webdriver.ChromeOptions()
options.add_argument("--headless")
options.add_argument("--no-sandbox")
options.add_argument("--disable-gpu")

options.add_argument("window-size=1920x1080")
options.add_argument("lang=ko_KR")
options.add_argument("user-agent=Chrome/77.0.3865.90")

driver.get("https://music.bugs.co.kr/chart")
source = driver.page_source

bs=BeautifulSoup(source,'html.parser')

table = bs.find('table', {'class':'list trackList byChart'})
tbody = table.tbody
#print(tbody)

#trs = tbody.find_all('tr')
#for title, artist, album in enumerate(trs):

top100_title = bs.find_all('p', class_="title")
top100_artist = bs.find_all('p', class_="artist")
top100_albumtitle = bs.find_all('a', class_="album")

artist = [bs.select_one('p:nth-of-type(1) a').text for bs in top100_artist]
title = [i.get_text().strip() for i in top100_title]
albumtitle = [i.get_text().strip() for i in top100_albumtitle]

#for i,tit in enumerate(title) :
#    print('%d : %s'%(i+1,tit))
for i,art in enumerate(artist):
        print('%d : %s' % (i+1, art))
    #중복되는 가수명 삭제하기    
#for i,alb in enumerate(albumtitle) :
#    print('%d : %s'%(i+1,alb))
    #1번째 row 제외시키기 (삽입할 때)


#del artist[1:6]

query = "drop table if exists musicList"
cur.execute(query)

query1 = """
    create table musicList(
    title varchar(100),
    artist varchar(100),
    albumtitle varchar(100),
    id varchar(100),
    primary key(id)
    );
"""
cur.execute(query1)

for i in range(0,100):
    query2 = """
    insert into musicList values ("%s", "%s", "%s", "%s"); """ %(str(title[i]), str(artist[i]), str(albumtitle[i+1]), str(title[i])+str(albumtitle[i+1]))
    cur.execute(query2)


conn.commit()
cur.close()
conn.close()
driver.quit()

# C:\Users\user\Downloads\chromedriver_win32
