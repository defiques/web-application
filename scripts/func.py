def download(com, df, dto):
    import re
    import time
    import requests
    import math
    import os
    import collections

    from bs4 import BeautifulSoup
    from selenium import webdriver
    from striprtf.striprtf import rtf_to_text
    from datetime import datetime

    url = "http://sudact.ru/arbitral/?arbitral-txt=" + com + "&arbitral-date_from=" + df + "&arbitral-date_to=" + dto

    br = webdriver.Chrome()
    br.maximize_window()
    br.get(url)

    path = "D:\\wamp64\\www\\dip\\DOCS\\"

    form = br.find_element_by_id("arbitral-searchForm")
    form.submit()
    time.sleep(3)

    bs = BeautifulSoup(br.page_source, features="lxml")
    n_docs = bs.find("div", {"class": "prompting"})

    n = ''
    for i in filter(str.isdigit, str(n_docs)):
        n = n + i
    n = math.ceil(int(n) / 10)

    slink = '/'
    elink = '/?arbitral-txt='
    dwnlink = []
    doc_name = {}
    doc_date = {}

    if n == 1:
        for h4 in bs.find_all("h4"):
            for a in h4.find_all("a", href=True, target="_blank"):
                result = a['href']
                link = re.search('%s(.*)%s' % (slink, elink), result).group(1)
                link = link[:26]
                date = a.text
                doc_name['sudact.ru/' + link] = date
                date = re.search('%s(.*)%s' % ('от ', ' г.'), date).group(1)
                doc_date['sudact.ru/' + link] = date
                dwnlink.append('http://sudact.ru/' + link[:12] + '/save/' + link[13:])
        time.sleep(2)
    else:
        for i in range(1, n + 1):
            for h4 in bs.find_all("h4"):
                for a in h4.find_all("a", href=True, target="_blank"):
                    result = a['href']
                    link = re.search('%s(.*)%s' % (slink, elink), result).group(1)
                    link = link[:26]
                    date = a.text
                    doc_name['sudact.ru/' + link] = date
                    date = re.search('%s(.*)%s' % ('от ', ' г.'), date).group(1)
                    doc_date['sudact.ru/' + link] = date
                    dwnlink.append('http://sudact.ru/' + link[:12] + '/save/' + link[13:])
            br.find_element_by_class_name("page-next").click()
            time.sleep(2)
            bs = BeautifulSoup(br.page_source, features="lxml")

    q = 0

    dwnlink = sorted(dwnlink)
    doc_date = dict(collections.OrderedDict(sorted(doc_date.items())))
    doc_name = dict(collections.OrderedDict(sorted(doc_name.items())))

    for key, value in doc_date.items():
        if os.access(path + com, os.F_OK):
            os.chdir(path + com)
        else:
            os.mkdir(path + com)
            os.chdir(path + com)
        doc = requests.post(dwnlink[q])
        with open(re.sub(r'[/?]', '!', key + '.txt'), 'w', encoding='utf-8') as f:
            f.write(rtf_to_text(doc.text))
        q = q + 1
        br.refresh()
        time.sleep(2)
    br.quit()

    RU_MONTH_VALUES = {
        'января': 1,
        'февраля': 2,
        'марта': 3,
        'апреля': 4,
        'мая': 5,
        'июня': 6,
        'июля': 7,
        'августа': 8,
        'сентября': 9,
        'октября': 10,
        'ноября': 11,
        'декабря': 12,
    }

    def int_value_from_ru_month(date_str):
        for k, v in RU_MONTH_VALUES.items():
            date_str = date_str.replace(k, str(v))

        return date_str

    for key, value in doc_date.items():
        d = int_value_from_ru_month(value)
        d = datetime.strptime(d, '%d %m %Y')
        print(d.strftime('%Y-%m-%d'))

    with open('D:\\wamp64\\www\\dip\\txt\\doc_name.txt', 'w') as f:
        for key, name in doc_name.items():
            f.write(name + '\n')


def bayests(al):
    # tokenizing + stemming + tf-ifd method + classification

    import os
    import io
    import nltk
    import string
    import pymorphy2
    import numpy as np

    from nltk.corpus import stopwords
    from sklearn.feature_extraction.text import TfidfTransformer
    from sklearn.feature_extraction.text import TfidfVectorizer
    from sklearn.feature_extraction.text import CountVectorizer
    from sklearn.naive_bayes import MultinomialNB
    from sklearn.linear_model import SGDClassifier
    from sklearn.neighbors import KNeighborsClassifier
    from sklearn.externals import joblib
    from pprint import pprint
    from inspect import getmembers

    text = []
    com_text = []
    com_text2 = []
    text_class_num = []
    trainname = []
    testname = []
    reshenie = []
    ttname = []
    class_num = 3
    i = 0

    # запись обучающей выборки
    while i < class_num:
        path = 'D:\\MPEI\\8sem\\DIPLOM(DONE)\\' + str(i + 1) + 'TXT' + '\\CHECK12WITHNEWFILES'
        for filename in os.listdir(path):
            with io.open(path + '\\' + filename, encoding='utf-8') as file:
                textfile = ''
                for line in file:
                    textfile = textfile + line
                text.append(textfile)
                text_class_num.append(i + 1)
            trainname.append(filename)
        i = i + 1

    # удаление знаков препинания и стоп-слов
    # stop_words = stopwords.words('russian')
    # stop_words.extend(['что', 'это', 'так', 'вот', 'быть', 'как', 'в', '—', 'к', 'на', 'суд', 'арбитражный',
    # 'дело', 'договор', 'ответчик', 'год', 'рф', 'ст', 'истец', 'требование', 'российский',
    # 'федерация', 'cтатья', 'кодекс', 'ответственность' ,'общество', 'лицо', 'который',
    # 'соответствие', 'обязательство', 'судебный', 'копа', 'закон', 'ооо'])
    for i in text:
        r = i.maketrans(string.punctuation, ' ' * len(string.punctuation))
        i = i.lower().translate(r)
        # i = ' '.join([word for word in i.split() if word not in (stop_words)])
        com_text.append(i)

    # стемминг
    morph = pymorphy2.MorphAnalyzer()
    for i in range(len(com_text)):
        com_text1 = ''
        lst = []
        lst = com_text[i].replace('.', '').split()
        for name in lst:
            com_text1 = com_text1 + morph.parse(name)[0].normal_form + ' '
        com_text2.append(com_text1)

    count_vect = CountVectorizer()
    tfidf_transformer = TfidfTransformer()
    tfidf_vect = TfidfVectorizer()

    X_train_counts = count_vect.fit_transform(com_text2)
    X_train_tfidf = tfidf_transformer.fit_transform(X_train_counts)
    # classifier = SGDClassifier()
    classifier = MultinomialNB(al)
    # classifier = KNeighborsClassifier(n_neighbors=7)
    clf = classifier.fit(X_train_counts, text_class_num)
    filename = 'bayes_counts.joblib.pkl'
    joblib.dump(count_vect, filename)
    filename = 'bayes_tfidf.joblib.pkl'
    joblib.dump(tfidf_transformer, filename)
    filename = 'bayes_classificator.joblib.pkl'
    joblib.dump(classifier, filename)


def svmts(mi):
    # tokenizing + stemming + tf-ifd method + classification

    import os
    import io
    import nltk
    import string
    import pymorphy2
    import numpy as np

    from nltk.corpus import stopwords
    from sklearn.feature_extraction.text import TfidfTransformer
    from sklearn.feature_extraction.text import TfidfVectorizer
    from sklearn.feature_extraction.text import CountVectorizer
    from sklearn.naive_bayes import MultinomialNB
    from sklearn.linear_model import SGDClassifier
    from sklearn.neighbors import KNeighborsClassifier
    from sklearn.externals import joblib
    from pprint import pprint
    from inspect import getmembers

    text = []
    com_text = []
    com_text2 = []
    text_class_num = []
    trainname = []
    testname = []
    reshenie = []
    ttname = []
    class_num = 3
    i = 0

    # запись обучающей выборки
    while i < class_num:
        path = 'D:\\MPEI\\8sem\\DIPLOM(DONE)\\' + str(i + 1) + 'TXT' + '\\CHECK12WITHNEWFILES'
        for filename in os.listdir(path):
            with io.open(path + '\\' + filename, encoding='utf-8') as file:
                textfile = ''
                for line in file:
                    textfile = textfile + line
                text.append(textfile)
                text_class_num.append(i + 1)
            trainname.append(filename)
        i = i + 1

    # удаление знаков препинания и стоп-слов
    # stop_words = stopwords.words('russian')
    # stop_words.extend(['что', 'это', 'так', 'вот', 'быть', 'как', 'в', '—', 'к', 'на', 'суд', 'арбитражный',
    # 'дело', 'договор', 'ответчик', 'год', 'рф', 'ст', 'истец', 'требование', 'российский',
    # 'федерация', 'cтатья', 'кодекс', 'ответственность' ,'общество', 'лицо', 'который',
    # 'соответствие', 'обязательство', 'судебный', 'копа', 'закон', 'ооо'])
    for i in text:
        r = i.maketrans(string.punctuation, ' ' * len(string.punctuation))
        i = i.lower().translate(r)
        # i = ' '.join([word for word in i.split() if word not in (stop_words)])
        com_text.append(i)

    # стемминг
    morph = pymorphy2.MorphAnalyzer()
    for i in range(len(com_text)):
        com_text1 = ''
        lst = []
        lst = com_text[i].replace('.', '').split()
        for name in lst:
            com_text1 = com_text1 + morph.parse(name)[0].normal_form + ' '
        com_text2.append(com_text1)

    count_vect = CountVectorizer()
    tfidf_transformer = TfidfTransformer()
    tfidf_vect = TfidfVectorizer()

    X_train_counts = count_vect.fit_transform(com_text2)
    X_train_tfidf = tfidf_transformer.fit_transform(X_train_counts)
    classifier = SGDClassifier('hinge', 'l2', 0.0001, 0.15, True, mi, 0.001, True, 0, 0.1,
                               None, None, 'optimal', 0.0, 0.5, False, 0.1, 5, None,
                               False, False)
    # classifier = MultinomialNB()
    # classifier = KNeighborsClassifier(n_neighbors=7)
    clf = classifier.fit(X_train_counts, text_class_num)

    filename = 'svm_classificator.joblib.pkl'
    joblib.dump(classifier, filename)


def kmeansts(kn):
    # tokenizing + stemming + tf-ifd method + classification

    import os
    import io
    import nltk
    import string
    import pymorphy2
    import numpy as np

    from nltk.corpus import stopwords
    from sklearn.feature_extraction.text import TfidfTransformer
    from sklearn.feature_extraction.text import TfidfVectorizer
    from sklearn.feature_extraction.text import CountVectorizer
    from sklearn.naive_bayes import MultinomialNB
    from sklearn.linear_model import SGDClassifier
    from sklearn.neighbors import KNeighborsClassifier
    from sklearn.externals import joblib
    from pprint import pprint
    from inspect import getmembers

    text = []
    com_text = []
    com_text2 = []
    text_class_num = []
    trainname = []
    testname = []
    reshenie = []
    ttname = []
    class_num = 3
    i = 0

    # запись обучающей выборки
    while i < class_num:
        path = 'D:\\MPEI\\8sem\\DIPLOM(DONE)\\' + str(i + 1) + 'TXT' + '\\CHECK12WITHNEWFILES'
        for filename in os.listdir(path):
            with io.open(path + '\\' + filename, encoding='utf-8') as file:
                textfile = ''
                for line in file:
                    textfile = textfile + line
                text.append(textfile)
                text_class_num.append(i + 1)
            trainname.append(filename)
        i = i + 1

    # удаление знаков препинания и стоп-слов
    # stop_words = stopwords.words('russian')
    # stop_words.extend(['что', 'это', 'так', 'вот', 'быть', 'как', 'в', '—', 'к', 'на', 'суд', 'арбитражный',
    # 'дело', 'договор', 'ответчик', 'год', 'рф', 'ст', 'истец', 'требование', 'российский',
    # 'федерация', 'cтатья', 'кодекс', 'ответственность' ,'общество', 'лицо', 'который',
    # 'соответствие', 'обязательство', 'судебный', 'копа', 'закон', 'ооо'])
    for i in text:
        r = i.maketrans(string.punctuation, ' ' * len(string.punctuation))
        i = i.lower().translate(r)
        # i = ' '.join([word for word in i.split() if word not in (stop_words)])
        com_text.append(i)

    # стемминг
    morph = pymorphy2.MorphAnalyzer()
    for i in range(len(com_text)):
        com_text1 = ''
        lst = []
        lst = com_text[i].replace('.', '').split()
        for name in lst:
            com_text1 = com_text1 + morph.parse(name)[0].normal_form + ' '
        com_text2.append(com_text1)

    count_vect = CountVectorizer()
    tfidf_transformer = TfidfTransformer()
    tfidf_vect = TfidfVectorizer()

    X_train_counts = count_vect.fit_transform(com_text2)
    X_train_tfidf = tfidf_transformer.fit_transform(X_train_counts)
    # classifier = SGDClassifier()
    # classifier = MultinomialNB()
    classifier = KNeighborsClassifier(kn)
    clf = classifier.fit(X_train_counts, text_class_num)

    filename = 'kmeans_classificator.joblib.pkl'
    joblib.dump(classifier, filename)


