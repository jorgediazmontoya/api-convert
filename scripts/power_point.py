#!/usr/bin/env python
import os
import sys
from win32com import client
import argparse

pttxFormatPDF = 32

parser = argparse.ArgumentParser()
parser.add_argument("-pf", "--pathfile", help="Ruta del archivo a procesar")
args = parser.parse_args()

if args.pathfile:
    input_folder_path = sys.argv[2]

    in_file = os.path.abspath(input_folder_path)

    out_file = os.path.splitext(in_file)[0]

    powerpoint = client.Dispatch("PowerPoint.Application")
    pdf = powerpoint.Presentations.Open(in_file, WithWindow=False)
    pdf.SaveAs(out_file, pttxFormatPDF)
    pdf.Close()
    powerpoint.Quit()
