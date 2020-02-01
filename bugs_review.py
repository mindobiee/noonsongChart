from selenium import webdriver
from bs4 import BeautifulSoup
import pymysql
from selenium.common.exceptions import ElementNotInteractableException
from selenium.webdriver.common.keys import Keys
import pandas as pd
import warnings

crawling_num = 0

chromedriver_dir = r'C:\Users\user\Downloads\chromedriver_win32\chromedriver.exe'
driver = webdriver.Chrome(chromedriver_dir)
driver.implicitly_wait(3)

# create cursor
conn = pymysql.connect(host="127.0.0.1",
                       user='root', passwd='960919', db="gradualDB")
cur = conn.cursor()

# options to look like a human
options = webdriver.ChromeOptions()
options.add_argument("--headless")
options.add_argument("--no-sandbox")
options.add_argument("--disable-gpu")

options.add_argument("window-size=1080x1080")
options.add_argument("lang=ko_KR")
options.add_argument("user-agent=Chrome/77.0.3865.90")

driver.get('https://music.bugs.co.kr/chart')
source = driver.page_source
bs = BeautifulSoup(source, 'html.parser')


def button() :
    # changing pages
    # //*[@id="comments"]/div/p[4]/a
    # /html/body/div[2]/div[2]/article/section[6]/div/p[4]/a

    global crawling_num
    try:
        next_page = driver.find_element_by_xpath("//*[@id='comments']/div/p[4]/a")
        next_page.send_keys(Keys.ENTER)
        driver.implicitly_wait(3)  # seconds

        crawling_num = crawling_num + 1
        print("# of changing pages :" + str(crawling_num))

    except ElementNotInteractableException: #interactable error
        return False
    return True


# Crawling comments :)
def crawlcomments(bs):

    # crawling (user, comment) except re-comment
    # 대댓글 처리하기 #comments > div > ul > li:nth-child(1) > div.comment > p
    users = bs.select('#comments > div > ul > li > span')
    listcomments = bs.select('#comments > div > ul > li > div.comment > p')
    
    users = [i.get_text().strip() for i in users]
    comments = [i.get_text().strip() for i in listcomments]
    

    # making id by using title, album
    title = driver.find_element_by_xpath("/html/body/div[2]/div[2]/article/header/div/h1").text.strip()
    album = driver.find_element_by_xpath(
        "/html/body/div[2]/div[2]/article/section[1]/div/div[1]/table/tbody/tr[3]/td/a").text.strip()

    like = bs.select_one('#container > section.sectionPadding.summaryInfo.summaryTrack > div > div.etcInfo > span > a > span > em').get_text()
    like = like.replace(",", "")
    print("like: " + like)
    # add into musiclist later

    # ignore unique key duplicate warning
    warnings.filterwarnings("ignore")

    newId = title.replace(',', '#').replace('&', '#').replace('(', '#').split('#')[0] + \
            album.replace(',', '#').replace('&', '#').replace('(', '#').split('#')[0]
    print("newId :" + newId)


    query = """
            update musicList set likes = "%s", comment = "%s" where id = "%s"; 
            """ % ( int(like), len(users), newId )
    cur.execute(query)

    # inserting the data into table columns & excel file
    result = []
    for i in range(len(users)):
        comments[i]=comments[i].replace("\"", "")
        query2 = """
        insert ignore into reviewList values ("%s", "%s", "%s"); """ % (
        str(users[i]), str(comments[i]), newId)
        cur.execute(query2)
        result.append([users[i], comments[i]])

    conn.commit()
    df = pd.DataFrame(result, columns=['users', 'comments'])
    df.to_excel("test.xlsx", encoding="utf-8")


# selecting the song number&show the pages
def change_songs(song_num):

    global crawling_num
    xpath_songs = "/html/body/div[2]/div[2]/article/section/div/div[1]/table/tbody/tr[" + str(song_num) + "]/td[4]/a"
    button_songs = driver.find_element_by_xpath(xpath_songs)
    button_songs.send_keys(Keys.ENTER)
    driver.implicitly_wait(3)

    # prerequsite : review Table exists, no need to make again, just update.
    '''    
    query = "drop table if exists reviewList"
    cur.execute(query)
    query1 = """
            create table reviewList(
            user varchar(100),
            comment varchar(500),
            id varchar(100),
            primary key (id, user)
            );
        """
    cur.execute(query1)
    '''
    source = driver.page_source
    bs = BeautifulSoup(source, 'html.parser')

    # the # 0f total comment
    totalcomment = driver.find_element_by_xpath("//*[@id='totalComment']").text
    print('total # of comments :' + str(totalcomment))

    # the # of comment in this page
    users = bs.find_all('span', class_="user")
    user = [i.get_text().strip() for i in users]
    comment_num = len(user)
    print('# of comments : ' + str(comment_num))

    # condition : if comment button doesn't exist, it stops
    while button():
        source = driver.page_source
        bs = BeautifulSoup(source, 'html.parser')

    crawlcomments(bs)
    print("crawling ends")

    # going back to main chart page
    driver.get('https://music.bugs.co.kr/chart')
    source = driver.page_source


# the start point of the whole program
if __name__ == '__main__':
    song_num = int(input("song_num :"))
    change_songs(song_num)
    # conn.commit()
    cur.close()
    conn.close()
    driver.quit()
