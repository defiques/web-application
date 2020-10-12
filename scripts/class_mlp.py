import tensorflow.compat.v1 as tf
import numpy as np
import os
import pymorphy2
import string
import io
import pickle
from collections import Counter

def multilayer_perceptron(input_tensor, weights, biases):
    layer_1_multiplication = tf.matmul(input_tensor, weights['h1'])
    layer_1_addition = tf.add(layer_1_multiplication, biases['b1'])
    layer_1 = tf.nn.relu(layer_1_addition)

    # Hidden layer with RELU activation
    layer_2_multiplication = tf.matmul(layer_1, weights['h2'])
    layer_2_addition = tf.add(layer_2_multiplication, biases['b2'])
    layer_2 = tf.nn.relu(layer_2_addition)

    # Output layer
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
    if category == 1:
        y[0] = 1.
    elif category == 2:
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

with open('D:\\wamp64\\www\\dip\\dumps\\totalwords.pickle', 'rb') as f:
    total_words = pickle.load(f)

text = []
com_text = []
com_text2 = []
text_class = []

tf.disable_v2_behavior()
vocab = Counter()

path = "D:\\wamp64\\www\\dip\\DOCS"
for folder in os.listdir(path):
    for filename in sorted(os.listdir(path + "\\" + folder)):
        with io.open(path + "\\" + folder + "\\" + filename, encoding='utf-8') as file:
            textfile = ''
            for line in file:
                textfile = textfile + line
            text.append(textfile)

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

for text in com_text2:
    for word in text.split(' '):
        vocab[word.lower()] += 1

word2index = get_word_2_index(vocab)
texts, classes = get_batch(com_text2, text_class, 0, len(com_text2))

n_input = total_words

saver = tf.train.Saver()

with tf.Session() as sess:
    saver.restore(sess, "/tmp/model.ckpt")
    print("Model restored.")

    classification = sess.run(tf.argmax(prediction, 1), feed_dict={input_tensor: texts})
    print("Predicted categories:", classification)