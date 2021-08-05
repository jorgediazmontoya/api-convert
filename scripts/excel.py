#!/usr/bin/env python
import argparse
import os
import sys
from win32com import client

parser = argparse.ArgumentParser()
parser.add_argument("-pf", "--pathfile", help="Ruta del archivo a procesar")
args = parser.parse_args()

if args.pathfile:
    excel = client.Dispatch("Excel.Application")
    input_folder_path = sys.argv[2]

    in_file = os.path.abspath(input_folder_path)
    out_file = os.path.splitext(in_file)[0]

    book = excel.Workbooks.Open(in_file)
    book.ExportAsFixedFormat(0, out_file)
    excel.Quit()
