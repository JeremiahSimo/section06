target_number = int(input("Set the target number between 1 and 20:"))

while target_number  <1 or target_number > 20:
    print("The number must be between 1 and 20")
    guess = int(input("Please enter a valid target number between 1 and 20"))

guess = None
attempts = 0
max_attempts = 5

print("Guess the number between 1 and 20., You have 5 attempts")
while guess != target_number and attempts < max_attempts:
    guess = int(input("Enter your guess:"))
    max_attempts += 1
    if target_number > guess :
        print("Too low! Try again.")

    elif  target_number < guess:
        print("Too high try again")

    else:
        print("Comngratulations! You guessed the number")
        break

if guess != target_number:
    print("Sorry, you've used all your attempts, The number was", target_number)
















