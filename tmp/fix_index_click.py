import os

filepath = r"c:\Users\djkyk\OneDrive\Desktop\sgqdj\homologacoes_mock\views\index.php"
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

new_content = content.replace('onclick="openCancelModal(', 'onclick="window.openCancelModal(')

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(new_content)

print("Replacement done in index.php")
