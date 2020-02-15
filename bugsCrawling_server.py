from selenium import webdriver
from bs4 import BeautifulSoup
import pymysql
import time
import warnings
from selenium.common.exceptions import ElementNotInteractableException
from selenium.webdriver.common.keys import Keys
from webdriver_manager.chrome import ChromeDriverManager

crawling_num = 0
rank = 0
tmp = 0
artist =[]
title =[]
albumTitle=[]
Ids = []

# ignore unique key duplicate warning
warnings.filterwarnings("ignore")
conn = pymysql.connect(host = "127.0.0.1",
                       user = 'root', passwd = 'Song123~', db = 'gradproj', charset='utf8')
cur = conn.cursor()

# options to look like a human
options = webdriver.ChromeOptions()

options.add_argument("--headless")
options.add_argument("--no-sandbox")
options.add_argument("--disable-gpu")
options.add_argument("window-size=1920x1080")
options.add_argument("lang=ko_KR")
options.add_argument("user-agent=Chrome/77.0.3865.90")

driver = webdriver.Chrome(ChromeDriverManager().install(), options=options)

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

def crawlcomments(bs,latest_comment,song_num):

    global rank
    global tmp
    rank += rank
    # crawling (user, comment) except re-comment
    users = bs.select('#comments > div > ul > li > span')
    listcomments = bs.select('#comments > div > ul > li > div.comment > p')
    comments = [i.get_text().strip() for i in listcomments]
    users = [i.get_text().strip() for i in users]
    like = bs.select_one('#container > section.sectionPadding.summaryInfo.summaryTrack > div > div.etcInfo > span > a > span > em').get_text()
    like = like.replace(",", "")
    print("like: " + like)

    newId = Ids[song_num-1]
    print("newId :" + newId)

    cur.execute("select like_sum from ex_musicList_bugs where id = %s;", newId)  # 이전 like_sum
    temp = cur.fetchall()
    if len(temp) == 0:
        like_cnt = 0
    else:
        like_cnt = int(like) - int(temp[0][0])

    cur.execute("select comments_sum from ex_musicList_bugs where id = %s;", newId)
    temp = cur.fetchall()
    if len(temp) == 0:
        comment_cnt = 0
    else:
        comment_cnt = int(len(users)) - int(temp[0][0])
    img_url = 'https:' + bs.find(class_='photos').find("a").get('href')

    query = """
           update musicList_bugs set like_sum = "%s", like_cnt = "%s", comments_sum ="%s", comments_cnt="%s", img_url ="%s" where id = "%s"; 
            """ % (int(like), int(like_cnt), len(users), int(comment_cnt), newId, str(img_url))
    cur.execute(query)

    for i in range(len(users)) :
        if (latest_comment[0] == users[i]) and (latest_comment[1] == comments[i]):
            return  # no need to crawl
        comments[i] = comments[i].replace("\"", "")
        query2 = """
        insert ignore into comments_bugs (id, writerId, comment) values ("%s", "%s", "%s"); """ % (
        newId, str(users[i]), str(comments[i]))
        cur.execute(query2)

    conn.commit()

def change_songs(song_num):

    global crawling_num
    global Ids
    xpath_songs = "/html/body/div[2]/div[2]/article/section/div/div[1]/table/tbody/tr[" + str(song_num) + "]/td[4]/a"
    button_songs = driver.find_element_by_xpath(xpath_songs)
    button_songs.send_keys(Keys.ENTER)
    driver.implicitly_wait(1)

    source = driver.page_source
    bs = BeautifulSoup(source, 'html.parser')
    print("crawling #", song_num)

    cur.execute("select writerId, comment from comments_bugs where id = %s order by time_of_crawl limit 1;", Ids[song_num-1])
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
    # crawling again !

    crawlcomments(bs, latest_comment, song_num)
    print("crawling ends")

    # going back to main chart page
    driver.get('https://music.bugs.co.kr/chart')
    source = driver.page_source


def crawl_comments(bs, latest_comment):

    global tmp
    users = bs.select('#comments > div > ul > li > span')
    listComments = bs.select('#comments > div > ul > li > div.comment > p')
    comments = [i.get_text().strip() for i in listComments]
    users = [i.get_text().strip() for i in users]

    for i in range(len(users)) :
        if(latest_comment[0] == users[i]) and (latest_comment[1] == comments[i]):
            tmp = i
            return True
    return False

if __name__ == '__main__':

    top100_title = bs.find_all('p', class_="title")
    top100_artist = bs.find_all('p', class_="artist")
    top100_albumTitle = bs.find_all('a', class_="album")

    artist = [i.select_one('a').text for i in top100_artist]
    title = [i.get_text().strip() for i in top100_title]
    albumTitle = [i.get_text().strip() for i in top100_albumTitle]

    #for i,tit in enumerate(title) :
    #    print('%d : %s'%(i+1,tit))
    for i, art in enumerate(artist):
        print('%d: %s' % (i + 1, art))
    #for i,alb in enumerate(albumTitle) :
    #    print('%d : %s'%(i+1,alb))

    query = "drop table if exists ex_musicList_bugs"
    cur.execute(query)

    query0 = "RENAME TABLE musicList_bugs TO ex_musicList_bugs"
    cur.execute(query0)

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
           primary key(ranking)
       )ENGINE=InnoDB DEFAULT CHARSET=utf8;
    """
    cur.execute(create_table_musicList_bugs)

    for i in range(0, 100):
        newId = title[i].replace('`','\'').replace('’','\'').replace(',', '#').replace('&', '#').replace('(', '#').replace('[', '#').replace('<', '#').replace('{', '#').split('#')[0] + \
                albumTitle[i + 1].replace('`','\'').replace('’','\'').replace('<', '').replace('{', '').replace('(', '').replace('[', '').split(' ')[0]
        # 괄호 없애고, albumTitle -> 띄어쓰기 전까지만 출력
        Ids.append(newId)
        query2 = """
        insert into musicList_bugs
        values ("%s", "%s", "%s", "%s", "%s", "%s", "%s","%s", "%s","%s"); 
        """ % (newId ,str(title[i]), str(artist[i]), str(albumTitle[i + 1]), i + 1, 0, 0, 0, 0, 0)
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
