#bugs_server.py 벅스크롤링 최종 파일명

from selenium import webdriver
from bs4 import BeautifulSoup
import pymysql
import time
import warnings
from selenium.common.exceptions import ElementNotInteractableException
from selenium.webdriver.common.keys import Keys
from webdriver_manager.chrome import ChromeDriverManager

print(time.strftime('%c', time.localtime(time.time())))

# ignore unique key duplicate warning
warnings.filterwarnings("ignore")

# make a cursor
conn = pymysql.connect(host="127.0.0.1", user='root', passwd='Song123~', db='gradproj', charset='utf8')
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
bs = BeautifulSoup(source, 'html.parser')

# ignore unique key duplicate warning
warnings.filterwarnings("ignore")

#top100_title = bs.find_all('p', class_="title")
#top100_artist = bs.find_all('p', class_="artist")
#top100_albumTitle = bs.find_all('a', class_="album")
#song_urls = bs.find_all('a', class_="trackInfo")

#song_url = [i.get('href') for i in song_urls]
#artist = [i.select_one('a').text for i in top100_artist]
#title = [i.get_text().strip() for i in top100_title]
#albumTitle = [i.get_text().strip() for i in top100_albumTitle]
#for i in range(0,100):
#    newId[i] = title[i].replace('`', '\'').replace('‘', '\'').replace(',', '#').replace('&', '#').replace('(', '#').replace(
#                '[', '#').replace('<', '#').replace('{', '#').split('#')[0].replace(' ', '') + \
#            albumTitle[i + 1].replace('`', '\'').replace('‘', '\'').replace('<', '').replace('{', '').replace('(',                                                                                                          '').replace(
#                '[', '').split(' ')[0].replace(' ', '')

for i in range(0, 50):

    song_num = i + 1
    # page 전환
    xpath_songs = "/html/body/div[2]/div[2]/article/section/div/div[1]/table/tbody/tr[" + str(song_num) + "]/td[4]/a"
    button_songs = driver.find_element_by_xpath(xpath_songs)
    button_songs.send_keys(Keys.ENTER)
    driver.implicitly_wait(1)
    tmp = -1
    source = driver.page_source
    bs = BeautifulSoup(source, 'html.parser')

    print("crawling #", song_num)
    #title, albumTitle,artist,song_url
    title=driver.find_element_by_xpath('/html/body/div[2]/div[2]/article/header/div/h1').text.strip()
    artist=driver.find_element_by_xpath('/html/body/div[2]/div[2]/article/section[1]/div/div[1]/table/tbody/tr[1]/td/a').text.strip()
    try:
        albumTitle=driver.find_element_by_xpath('/html/body/div[2]/div[2]/article/section[1]/div/div[1]/table/tbody/tr[3]/td/a').text.strip()
    except :
        albumTitle=driver.find_element_by_xpath('/html/body/div[2]/div[2]/article/section[1]/div/div[1]/table/tbody/tr[2]/td/a').text.strip()

    Id = title.replace('`', '\'').replace('‘', '\'').replace(',', '#').replace('&', '#').replace('(', '#').replace(
                    '[', '#').replace('<', '#').replace('{', '#').split('#')[0].replace(' ', '') + \
    albumTitle.replace('`', '\'').replace('‘', '\'').replace('<', '').replace('{', '').replace('(',                                                                                                          '').replace(
                  '[', '').split(' ')[0].replace(' ', '')

    # 가장 최근에 크롤링한 id와 comment의 값을 가져온다. (크롤링 시간 단축용)
    cur.execute("select writerId, comment from comments_bugs where id = %s order by time_of_crawl desc limit 1;", Id)
    latest_comment = cur.fetchall()
    if len(latest_comment) == 0:
        latest_comment = [","]
    else:
        latest_comment = latest_comment[0]

    # id가 newId인 comments_bugs 테이블의 칼럼의 갯수를 센다. (comment_cnt와 sum 계산을 위해)
    cur.execute("select count(*) from comments_bugs where id = %s;", Id)
    pre_comments_sum = cur.fetchall()
    if len(pre_comments_sum) == 0:
        pre_comments_sum = 0
    else:
        pre_comments_sum = pre_comments_sum[0][0]
    # -----------------------press button til end
    while 1:
        try:
            next_page = driver.find_element_by_xpath("//*[@id='comments']/div/p[4]/a")
            next_page.send_keys(Keys.ENTER)
            driver.implicitly_wait(1)  # seconds
        # interactable error
        except ElementNotInteractableException:
            break
        time.sleep(1)

    # -----------------crawling part
    source = driver.page_source
    bs = BeautifulSoup(source, 'html.parser')
    users = bs.select('#comments > div > ul > li > span')
    listComments = bs.select('#comments > div > ul > li > div.comment > p')

    users = [k.get_text().strip() for k in users]
    comments = [t.get_text().strip() for t in listComments]


    # --------------------compare & insert comments
    for j in range(len(users)):
        if (latest_comment[0] == users[j]) and (latest_comment[1] == comments[j]):
            break
        else:
            comments[j] = comments[j].replace("\"", "")
            query2 = """
               insert ignore into comments_bugs (id, writerId, comment) values ("%s", "%s", "%s"); """ % (
                Id, str(users[j]), str(comments[j]))
            cur.execute(query2)
            conn.commit()

    print("crawling ends")

    like = bs.select_one(
        '#container > section.sectionPadding.summaryInfo.summaryTrack > div > div.etcInfo > span > a > span > em').get_text()
    like = like.replace(",", "")
    # like를 가져오고, 이전의 like를 가져와서 like의 증가수를 확인한다.
    #i = song_num - 1

    cur.execute("select like_sum from musicList_bugs where id = %s limit 1;", Id)
    pre_like = cur.fetchall()
    if len(pre_like) == 0:
        like_cnt = 0
    else:
        like_cnt = int(like) - pre_like[0][0]
    # print("like_s:"+ str(like) +", like_c:"+ str(like_cnt))

    # 추가된 comments 가 얼마나 되는지 확인한다.
    cur.execute("select count(*) from comments_bugs where id = %s;", Id)
    comments_sum = cur.fetchall()[0][0]
    if pre_comments_sum == 0:
        comments_cnt = 0
    else:
        comments_cnt = comments_sum - pre_comments_sum
    # print("co_sum:"+ str(comments_sum) +", co_cnt:"+ str(comments_cnt))

    img_url = bs.find(class_='photos').find('img').get('src')
    song_url=bs.head.find('meta',{'property':'og:url'}).get('content')

    query = """replace into musicList_bugs values (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s );"""
    data = (Id, str(title), str(artist), str(albumTitle),
                int(song_num), int(like), int(like_cnt), int(comments_sum),
                int(comments_cnt), str(img_url), str(song_url))
    cur.execute(query, data)
    conn.commit()


    # going back to main chart page
    driver.get('https://music.bugs.co.kr/chart')
    source = driver.page_source

conn.commit()
cur.close()
conn.close()
driver.quit()
print(time.strftime('%c', time.localtime(time.time())))
