#tokenizing + stemming + tf-ifd method + classification

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

#запись обучающей выборки 
while i < class_num:
    path = 'D:\\MPEI\\8sem\\DIPLOM(DONE)\\'+ str(i + 1) + 'TXT' + '\\CHECK12WITHNEWFILES'
    for filename in os.listdir(path):
        with io.open(path + '\\' + filename, encoding='utf-8') as file:
            textfile = ''
            for line in file:
                textfile = textfile + line
            text.append(textfile)
            text_class_num.append(i + 1)
        trainname.append(filename)    
    i = i + 1

#удаление знаков препинания и стоп-слов
#stop_words = stopwords.words('russian')
#stop_words.extend(['что', 'это', 'так', 'вот', 'быть', 'как', 'в', '—', 'к', 'на', 'суд', 'арбитражный',
                   #'дело', 'договор', 'ответчик', 'год', 'рф', 'ст', 'истец', 'требование', 'российский',
                   #'федерация', 'cтатья', 'кодекс', 'ответственность' ,'общество', 'лицо', 'который',
                   #'соответствие', 'обязательство', 'судебный', 'копа', 'закон', 'ооо'])
for i in text:
    r = i.maketrans(string.punctuation, ' ' * len(string.punctuation))
    i = i.lower().translate(r)
    #i = ' '.join([word for word in i.split() if word not in (stop_words)])
    com_text.append(i)

#стемминг
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
classifier = SGDClassifier()
#classifier = MultinomialNB()
#classifier = KNeighborsClassifier(n_neighbors=7)
clf = classifier.fit(X_train_tfidf, text_class_num)

text = []
com_text = []
com_text2 = []
test_class_num = []
#class_num = 5
i = 3

#запись тренировочной выборки
path = "D:\\wamp64\\www\\dip\\DOCS"
for folder in os.listdir(path):
    for filename in os.listdir(path + "\\" + folder):
        with io.open(path + "\\" + folder + "\\" + filename, encoding='utf-8') as file:
            textfile = ''
            for line in file:
                textfile = textfile + line
            text.append(textfile)
        testname.append(filename)

#удаление знаков препинания и стоп-слов
#stop_words = stopwords.words('russian')
#stop_words.extend(['что', 'это', 'так', 'вот', 'быть', 'как', 'в', '—', 'к', 'на', 'суд', 'арбитражный',
                   #'дело', 'договор', 'ответчик', 'год', 'рф', 'ст', 'истец', 'требование', 'российский',
                   #'федерация', 'cтатья', 'кодекс', 'ответственность' ,'общество', 'лицо', 'который',
                   #'соответствие', 'обязательство', 'судебный', 'копа', 'закон', 'ооо'])

for i in text:
    r = i.maketrans(string.punctuation, ' ' * len(string.punctuation))
    i = i.lower().translate(r)
    #i = ' '.join([word for word in i.split() if word not in (stop_words)])
    com_text.append(i)

#стемминг
morph = pymorphy2.MorphAnalyzer()    
for i in range(len(com_text)):
    com_text1 = ''
    lst = []
    lst = com_text[i].replace('.', '').split()
    for nam in lst:
        com_text1 = com_text1 + morph.parse(nam)[0].normal_form + ' '
    com_text2.append(com_text1)

X_new_train_counts = count_vect.transform(com_text2)
X_new_train_tfidf = tfidf_transformer.transform(X_new_train_counts)
predicted = clf.predict(X_new_train_tfidf)
b = ''
for i in str(list(predicted)):
    r = i.maketrans(string.punctuation, ' ' * len(string.punctuation))
    i = i.lower().translate(r)
    if i == ' ':
        i = ''
    b = b + i
print(b)
