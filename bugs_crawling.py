import pandas as pd
from selenium import webdriver
from bs4 import BeautifulSoup
import pymysql
import time
import warnings
from selenium.common.exceptions import ElementNotInteractableException
from selenium.webdriver.common.keys import Keys

crawling_num = 0
rank = 0

# ignore unique key duplicate warning
warnings.filterwarnings("ignore")

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

def button() :
    # changing pages
    global crawling_num
    try:
        next_page = driver.find_element_by_xpath("//*[@id='comments']/div/p[4]/a")
        next_page.send_keys(Keys.ENTER)
        driver.implicitly_wait(3)  # seconds
        crawling_num = crawling_num + 1
        print("# of changing pages :" + str(crawling_num))

    # interactable error
    except ElementNotInteractableException:
        return False
    return True

def crawlcomments(bs):

    global rank
    rank += rank
    # crawling (user, comment) except re-comment
    users = bs.select('#comments > div > ul > li > span')
    listcomments = bs.select('#comments > div > ul > li > div.comment > p')
    comments = [i.get_text().strip() for i in listcomments]
    users = [i.get_text().strip() for i in users]

    # making id by using title, album
    title = driver.find_element_by_xpath("/html/body/div[2]/div[2]/article/header/div/h1").text.strip()
    verify = driver.find_element_by_xpath("//*[@id='container']/section[1]/div/div[1]/table/tbody/tr[2]/th").text.strip()
    if verify == '앨범':
        album = driver.find_element_by_xpath(
            "/html/body/div[2]/div[2]/article/section[1]/div/div[1]/table/tbody/tr[2]/td/a").text.strip()
    else :
        album = driver.find_element_by_xpath(
        "/html/body/div[2]/div[2]/article/section[1]/div/div[1]/table/tbody/tr[3]/td/a").text.strip()

    like = bs.select_one('#container > section.sectionPadding.summaryInfo.summaryTrack > div > div.etcInfo > span > a > span > em').get_text()
    like = like.replace(",", "")
    print("like: " + like)

    # ignore unique key duplicate warning
    warnings.filterwarnings("ignore")

    newId = title.replace(',', '#').replace('&', '#').replace('(', '#').split('#')[0] + \
            album.replace(',', '#').replace('&', '#').replace('(', '#').split('#')[0]
    print("newId :" + newId)

    cur.execute("select likes from musiclist where id = %s;", newId)  # 이전 like_sum
    temp = cur.fetchall()
    if len(temp) == 0:
        like_cnt = 0
    else:
        like_cnt = int(like) - int(temp[0][0])


    query = """
           update musiclist set likes = "%s", like_cnt = "%s", comment ="%s" where id = "%s"; 
            """ % (int(like), int(like_cnt), len(users), newId)
    cur.execute(query)

    # inserting the data into table columns & excel file
    result = []
    for i in range(len(users)):
        comments[i]=comments[i].replace("\"", "")
        query2 = """
        insert ignore into reviewlist (id, user, comment) values ("%s", "%s", "%s"); """ % (
        newId, str(users[i]), str(comments[i]))
        cur.execute(query2)
        result.append([users[i], comments[i]])

    conn.commit()
    df = pd.DataFrame(result, columns=['users', 'comments'])
    df.to_excel("test.xlsx", encoding="utf-8")


def change_songs(song_num):

    global crawling_num
    xpath_songs = "/html/body/div[2]/div[2]/article/section/div/div[1]/table/tbody/tr[" + str(song_num) + "]/td[4]/a"
    button_songs = driver.find_element_by_xpath(xpath_songs)
    button_songs.send_keys(Keys.ENTER)
    driver.implicitly_wait(1)

    source = driver.page_source
    bs = BeautifulSoup(source, 'html.parser')

    cur.execute("select user, comment from reviewlist where id = %s order by time_of_crawl limit 1;", newId)
    latest_comment = cur.fetchall()

    if len(latest_comment) == 0:
        latest_comment =[","]
    else :
        latest_comment = latest_comment[0]


    # condition : if comment button doesn't exist, it stops
    while (button()):
        if crawl_comments(bs, latest_comment):
            break
        time.sleep(1)
        source = driver.page_source
        bs = BeautifulSoup(source, 'html.parser')

    crawlcomments(bs)
    print("crawling ends")

    # going back to main chart page
    driver.get('https://music.bugs.co.kr/chart')
    source = driver.page_source


def crawl_comments(bs, latest_comment):

    users = bs.select('#comments > div > ul > li > span')
    listcomments = bs.select('#comments > div > ul > li > div.comment > p')
    comments = [i.get_text().strip() for i in listcomments]
    users = [i.get_text().strip() for i in users]

    for i in range(len(users)) :
        if(latest_comment[0] == users[i]) and (latest_comment[1] == comments[i]):
            return True

    return False

if __name__ == '__main__':

    top100_title = bs.find_all('p', class_="title")
    top100_artist = bs.find_all('p', class_="artist")
    top100_albumtitle = bs.find_all('a', class_="album")

    artist = [bs.select_one('p:nth-of-type(1) a').text for bs in top100_artist]
    title = [i.get_text().strip() for i in top100_title]
    albumtitle = [i.get_text().strip() for i in top100_albumtitle]

    # for i,tit in enumerate(title) :
    #    print('%d : %s'%(i+1,tit))
    for i, art in enumerate(artist):
        print('%d: %s' % (i + 1, art))
    # for i,alb in enumerate(albumtitle) :
    #    print('%d : %s'%(i+1,alb))

    query = "drop table if exists musiclist"
    cur.execute(query)

    create_table_musiclist = """
        create table musiclist(
            title varchar(100) not null,
            artist varchar(100) not null,
            album_title varchar(100) not null,
            id varchar(100),
            ranking INT,
            likes INT,
            like_cnt INT,
            comment INT,
            primary key(ranking)
        );
    """
    cur.execute(create_table_musiclist)

    for i in range(0, 100):
        newId = title[i].replace(',', '#').replace('&', '#').replace('(', '#').split('#')[0] + \
                albumtitle[i + 1].replace(',', '#').replace('&', '#').replace('(', '#').split('#')[0]
        query2 = """
        insert into musiclist 
        values ("%s", "%s", "%s", "%s", "%s", "%s", "%s","%s"); 
        """ % (str(title[i]), str(artist[i]), str(albumtitle[i + 1]), newId, i + 1, 0, 0, 0)
        cur.execute(query2)

    print("crawling starts")
    i = 1
    while i < 101:
        change_songs(i)
        i = i+1

    conn.commit()
    cur.close()
    conn.close()
    driver.quit()
