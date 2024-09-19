numbers= [1,2,3,4,5,6,7,8,9]

for num in numbers:
    if num== 4:
        pass
        print(f"Skipped doing anything for {num} (pass)")
    elif num == 6:
        continue 
        print(f"This will not print for {num} (continue)")
    elif num == 8:
        break
        print(f"This will not print for {num} (break)")
    print(f"Processing number {num}")               
print("Loop Finished!")



    