from selenium import webdriver
from bs4 import BeautifulSoup
import pymysql
from selenium.webdriver.common.by import By
from selenium.webdriver.common.keys import Keys
import pandas as pd
from selenium.webdriver.support.wait import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC

crawling_num = 0
comment_num = 0

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
options.add_argument("window-size=1920x1080")
options.add_argument("lang=ko_KR")
options.add_argument("user-agent=Chrome/77.0.3865.90")

driver.get('https://music.bugs.co.kr/chart')
source = driver.page_source
bs = BeautifulSoup(source, 'html.parser')


# Crawling comments :)
def crawlcomments(bs):

    # crawling (user, comment)
    users = bs.find_all('span', class_="user")
    listcomments = bs.find_all('div', class_="comment")
    comments = [i.find("p").text.strip() for i in listcomments]
    users = [i.get_text().strip() for i in users]

    # making id by using title, album
    title = driver.find_element_by_xpath("/html/body/div[2]/div[2]/article/header/div/h1").text.strip()
    album = driver.find_element_by_xpath(
        "/html/body/div[2]/div[2]/article/section[1]/div/div[1]/table/tbody/tr[3]/td/a").text.strip()

    # inserting the data into table columns & excel file
    result = []
    for i in range(len(users)):
        query2 = """
        insert into reviewList values ("%s", "%s", "%s"); """ % (
        str(users[i]), str(comments[i + 1]), str(title) + str(album))
        cur.execute(query2)
        result.append([users[i], comments[i + 1]])

    conn.commit()
    df = pd.DataFrame(result, columns=['users', 'comments'])
    df.to_excel("test.xlsx", encoding="utf-8")


# selecting the song number&show the pages
def change_songs(song_num):

    global comment_num
    global crawling_num
    xpath_songs = "/html/body/div[2]/div[2]/article/section/div/div[1]/table/tbody/tr[" + str(song_num) + "]/td[4]/a"
    button_songs = driver.find_element_by_xpath(xpath_songs)
    button_songs.send_keys(Keys.ENTER)
    driver.implicitly_wait(3)

    # making a table(reviewlist)
    query = "drop table if exists reviewList"
    cur.execute(query)
    query1 = """
            create table reviewList(
            user varchar(100),
            comment varchar(500),
            id varchar(100)
            primary key (id, user)
            );
        """
    # foreign key(id) references musicList(id)
    cur.execute(query1)
    source = driver.page_source
    bs = BeautifulSoup(source, 'html.parser')

    totalcomment = driver.find_element_by_xpath("//*[@id='totalComment']").text
    print('total # of comments :' + str(totalcomment))

    users = bs.find_all('span', class_="user")
    user = [i.get_text().strip() for i in users]
    comment_num = len(user)
    print('# of comments : ' + str(comment_num))

    # if # of comments differ from totalcomment
    while int(comment_num) != int(totalcomment):

        # changing pages
        next_page = driver.find_element_by_xpath("//*[@id='comments']/div/p[4]/a")
        # //*[@id="comments"]/div/p[4]/a
        # /html/body/div[2]/div[2]/article/section[6]/div/p[4]/a
        next_page.send_keys(Keys.ENTER)
        driver.implicitly_wait(3)  # seconds

        source = driver.page_source
        bs = BeautifulSoup(source, 'html.parser')

        # counting # of comments
        users = bs.find_all('span', class_="user")
        user = [i.get_text().strip() for i in users]
        comment_num = len(user)

        # verifying #of comments & changing pages
        print("comment num :" + str(comment_num))
        crawling_num = crawling_num + 1
        print("# of changing pages :" + str(crawling_num))

        listcomments = bs.find_all('div', class_="comment")
        comments = [i.find("p").text.strip() for i in listcomments]

        # verifying the crawling results(user, comment)
        for i in range(len(user)):
            print('%d : %s : %s' % (i, user[i], comments[i + 1]))

    crawlcomments(bs)
    print("crawling ends")

    # going back to main chart page
    driver.get('https://music.bugs.co.kr/chart')
    source = driver.page_source

    '''
  crawlComments(bs)
    while(change_pages()):
        source = driver.page_source
        bs = BeautifulSoup(source,'html.parser')
        crawlComments(bs)
    print("crawling ends") 
    
'''


# the start point of the whole program
if __name__ == '__main__':
    song_num = int(input("song_num :"))
    change_songs(song_num)

    # conn.commit()
    cur.close()
    conn.close()
    driver.quit()
