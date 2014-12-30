Stream-encryption
=================

Dev. LSFR1, geffe and RC4 algorythms


LSFR1
=================
LSFR1 - Linear feedback shift register. If you want to cipher any public information or data you should initialize LSFR1 with random init. key. This key should consists of 1 or 0 and have a length equal to 23. 

This register returns encrypted message, which you pasted into the message input. Also LSFR1 function brings back generated key, which has been used in encryption. 

geffe
=================
Geffe - name of encrypting algorythm, which ciphering data using three linear feedback shift regiters. It has three inputs, where user should enter public keys. So the first key should be equal to 32, second 30, and third 28 accordingly.

RC4
=================
Encrypting algorythm, which ciphering file. At the begining of the encrypting user should paste public key with numbers from 1 to 255. 
