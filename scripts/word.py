#!/usr/bin/env python
import os
import sys
from win32com import client
import argparse

wdFormatPDF = 17

parser = argparse.ArgumentParser()
parser.add_argument("-pf", "--pathfile", help="Ruta del archivo a procesar")
args = parser.parse_args()

if args.pathfile:
    input_folder_path = sys.argv[2]

    in_file = os.path.abspath(input_folder_path)

    out_file = os.path.splitext(in_file)[0]

    word = client.Dispatch('Word.Application')
    doc = word.Documents.Open(in_file)
    doc.SaveAs(out_file, FileFormat=wdFormatPDF)
    doc.Close()
    word.Quit()