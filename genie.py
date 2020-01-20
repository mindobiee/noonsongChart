from selenium import webdriver
from webdriver_manager.chrome import ChromeDriverManager
from bs4 import BeautifulSoup
from urllib.request import urlopen
import pymysql 

driver = webdriver.Chrome(ChromeDriverManager().install())

# create cursor

conn = pymysql.connect(host = "127.0.0.1",
                       user = 'root', passwd = '132365', db = 'gradproj')
cur = conn.cursor()

# options to look like a human
options = webdriver.ChromeOptions()
options.add_argument("--headless")
options.add_argument("--no-sandbox")
options.add_argument("--disable-gpu")

options.add_argument("window-size=1920x1080")
options.add_argument("lang=ko_KR")
options.add_argument("user-agent=Chrome/77.0.3865.90")

driver.get('https://genie.co.kr/chart/top200')
source = driver.page_source

bs = BeautifulSoup(source, 'html.parser')

top100_title = bs.find_all(class_="title ellipsis")
top100_artist = bs.find_all(class_="artist ellipsis")
top100_albumtitle = bs.find_all(class_="albumtitle ellipsis")

title = [ i.get_text().strip() for i in top100_title ]
artist = [  i.get_text() for i in top100_artist ]
albumTitle = [  i.get_text() for i in top100_albumtitle ] 

del artist[1:6]

query = "drop table if exists musicList"
cur.execute(query)

query1 = """
    create table musicList(
        title varchar(100),
        artist varchar(100),
        albumtitle varchar(100),
        primary key (title, albumtitle)
        );
    """
cur.execute(query1)

for i in range(0, 50):
    query2 = """insert into musicList values ( "%s", "%s", "%s" ) ; """%(str(title[i]), str(artist[i]), str(albumTitle[i]))
    cur.execute(query2)


nextPageButton = driver.find_element_by_link_text("51 ~ 100 위")
nextPageButton.click()
driver.implicitly_wait(30)

source = driver.page_source

bs = BeautifulSoup(source, 'html.parser')

top100_title = bs.find_all(class_="title ellipsis")
top100_artist = bs.find_all(class_="artist ellipsis")
top100_albumtitle = bs.find_all(class_="albumtitle ellipsis")

title = [ i.get_text().strip() for i in top100_title ]
artist = [  i.get_text() for i in top100_artist ]
albumTitle = [  i.get_text() for i in top100_albumtitle ] 
del artist[1:6]

for i in range(0, 50):
    query2 = """insert into musicList values ( "%s", "%s", "%s" ) ; """%(str(title[i]), str(artist[i]), str(albumTitle[i]))
    cur.execute(query2)

conn.commit()
cur.close()
conn.close()
driver.quit() 

