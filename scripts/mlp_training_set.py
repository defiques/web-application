import tensorflow.compat.v1 as tf
import numpy as np
import os
import pymorphy2
import string
import io
import pickle
from collections import Counter

tf.disable_v2_behavior()

def multilayer_perceptron(input_tensor, weights, biases):
    layer_1_multiplication = tf.matmul(input_tensor, weights['h1'])
    layer_1_addition = tf.add(layer_1_multiplication, biases['b1'])
    layer_1 = tf.nn.relu(layer_1_addition)

    layer_2_multiplication = tf.matmul(layer_1, weights['h2'])
    layer_2_addition = tf.add(layer_2_multiplication, biases['b2'])
    layer_2 = tf.nn.relu(layer_2_addition)

    out_layer_multiplication = tf.matmul(layer_2, weights['out'])
    out_layer_addition = out_layer_multiplication + biases['out']

    return out_layer_addition

def get_word_2_index(vocab):
    word2index = {}
    for i, word in enumerate(vocab):
        word2index[word] = i

    return word2index

def text_to_vector(text):
    layer = np.zeros(total_words, dtype=float)
    for word in text.split(' '):
        layer[word2index[word.lower()]] += 1

    return layer

def category_to_vector(category):
    y = np.zeros((3), dtype=float)
    if category == 0:
        y[0] = 1.
    elif category == 1:
        y[1] = 1.
    else:
        y[2] = 1.

    return y

def get_batch(dt, dc, i, batch_size):
    batches = []
    results = []
    texts = dt[i * batch_size:i * batch_size + batch_size]
    categories = dc[i * batch_size:i * batch_size + batch_size]

    for text in texts:
        layer = text_to_vector(text)
        batches.append(layer)

    for category in categories:
        y = category_to_vector(category)
        results.append(y)

    return np.array(batches), np.array(results)

vocab = Counter()

with open('D:\\wamp64\\www\\dip\\dumps\\traindata.pickle', 'rb') as f:
    com_text2 = pickle.load(f)

with open('D:\\wamp64\\www\\dip\\dumps\\testdata.pickle', 'rb') as f:
    com_text5 = pickle.load(f)

with open('D:\\wamp64\\www\\dip\\dumps\\trainclasses.pickle', 'rb') as f:
    text_class_num = pickle.load(f)

with open('D:\\wamp64\\www\\dip\\dumps\\testclasses.pickle', 'rb') as f:
    test_class_num = pickle.load(f)

text1 = []
com_text11 = []
com_text12 = []
text_class = []


path = "D:\\wamp64\\www\\dip\\DOCS"
for folder in os.listdir(path):
    for filename in sorted(os.listdir(path + "\\" + folder)):
        with io.open(path + "\\" + folder + "\\" + filename, encoding='utf-8') as file:
            textfile = ''
            for line in file:
                textfile = textfile + line
            text1.append(textfile)

#удаление знаков препинания и стоп-слов
#stop_words = stopwords.words('russian')
#stop_words.extend(['что', 'это', 'так', 'вот', 'быть', 'как', 'в', '—', 'к', 'на', 'суд', 'арбитражный',
                   #'дело', 'договор', 'ответчик', 'год', 'рф', 'ст', 'истец', 'требование', 'российский',
                   #'федерация', 'cтатья', 'кодекс', 'ответственность' ,'общество', 'лицо', 'который',
                   #'соответствие', 'обязательство', 'судебный', 'копа', 'закон', 'ооо'])

for i in text1:
    r = i.maketrans(string.punctuation, ' ' * len(string.punctuation))
    i = i.lower().translate(r)
    #i = ' '.join([word for word in i.split() if word not in (stop_words)])
    com_text11.append(i)

#стемминг
morph = pymorphy2.MorphAnalyzer()
for i in range(len(com_text11)):
    com_text1 = ''
    lst = []
    lst = com_text11[i].replace('.', '').split()
    for nam in lst:
        com_text1 = com_text1 + morph.parse(nam)[0].normal_form + ' '
    com_text12.append(com_text1)

for text in com_text2:
    for word in text.split(' '):
        vocab[word.lower()] += 1

for text in com_text5:
    for word in text.split(' '):
        vocab[word.lower()] += 1

for text in com_text12:
    for word in text.split(' '):
        vocab[word.lower()] += 1

word2index = get_word_2_index(vocab)

total_words = len(vocab)

# Параметры
learning_rate = 0.1  # Скорость обучения
training_epochs = 20  # Кол-во эпох
batch_size = 100  # Размер "батча" (пакета)
display_step = 1

# Архитектура нейросети
n_hidden_1 = 150  # 1-ый скрытый слой
n_hidden_2 = 150  # 2-ой скрытый словй
n_input = total_words  # Входнойnn слой (общее количество слов в словаре)
n_classes = 3  # Выходной слой (3 класса)

input_tensor = tf.placeholder(tf.float32, [None, n_input], name="input")
output_tensor = tf.placeholder(tf.float32, [None, n_classes], name="output")

# Веса и смещения
weights = {
    'h1': tf.Variable(tf.random_normal([n_input, n_hidden_1])),
    'h2': tf.Variable(tf.random_normal([n_hidden_1, n_hidden_2])),
    'out': tf.Variable(tf.random_normal([n_hidden_2, n_classes]))
}
biases = {
    'b1': tf.Variable(tf.random_normal([n_hidden_1])),
    'b2': tf.Variable(tf.random_normal([n_hidden_2])),
    'out': tf.Variable(tf.random_normal([n_classes]))
}

# Создание модели
prediction = multilayer_perceptron(input_tensor, weights, biases)

loss = tf.reduce_mean(tf.nn.softmax_cross_entropy_with_logits(logits=prediction, labels=output_tensor))
optimizer = tf.train.AdamOptimizer(learning_rate=learning_rate).minimize(loss)
init = tf.global_variables_initializer()

# Утилита для сохранения обученной модели
# saver = tf.train.Saver()

# Запуск модели
with tf.Session() as sess:
    sess.run(init)

    # Процесс обучения
    for epoch in range(training_epochs):
        avg_cost = 0.
        total_batch = int(len(com_text2) / batch_size)
        # Цикл по всем "батчам"
        for i in range(total_batch):
            batch_x, batch_y = get_batch(com_text2, text_class_num, i, batch_size)
            c, _ = sess.run([loss, optimizer], feed_dict={input_tensor: batch_x, output_tensor: batch_y})
            avg_cost += c / total_batch
        # Отображение обучения по эпохам
        # if epoch % display_step == 0:
        #     print("Эпоха:", '%04d' % (epoch + 1), "ошибка=", \
        #           "{:.9f}".format(avg_cost))
    # print("Обучение окончено")

    texts, classes = get_batch(com_text12, text_class, 0, len(com_text12))
    classification = sess.run(tf.argmax(prediction, 1), feed_dict={input_tensor: texts})
    # print("Спрогнозированные категории:", classification)

    for i in range(len(classification)):
        classification[i] += 1


    b = ''
    for i in str(list(classification)):
        r = i.maketrans(string.punctuation, ' ' * len(string.punctuation))
        i = i.lower().translate(r)
        if i == ' ':
            i = ''
        b = b + i
    print(b)

    # # Процесс тестирования
    # correct_prediction = tf.equal(tf.argmax(prediction, 1), tf.argmax(output_tensor, 1))
    # # Расчет точности
    # accuracy = tf.reduce_mean(tf.cast(correct_prediction, "float"))
    # total_test_data = len(test_class_num)
    # batch_x_test, batch_y_test = get_batch(com_text5, test_class_num, 0, total_test_data)
    # print("Точность:", accuracy.eval({input_tensor: batch_x_test, output_tensor: batch_y_test}))

    # Сохранение обученной модели
    # save_path = saver.save(sess, "D:\\wamp64\\www\\dip\\models\\model.ckpt")
    # print("Модель сохранена в: %s" % save_path)