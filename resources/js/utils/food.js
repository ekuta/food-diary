
// format: YYYYMMDD
export const getUnits = (food) => {
  let units = [];
  if (food.usage_type & 0x1) {
    units.push('g');
  }
  if (food.usage_type & 0x2) {
    units.push('大さじ');
    units.push('小さじ');
  }
  if (food.usage_type & 0x4) {
    units.push('ml');
  }
  if (food.usage_type & 0x8) {
    units.push(food.package_unit);
  }
  return units;
}

export const getNewRecipeId = (diary, mealType)  => {
}

export const createDiaryItem = (input) => {
  const food = input.food;
  let unit = input.unit;
  let amount = +input.amount;
  let rate;

  if (unit == '小さじ') {
    unit = 'ml';
    amount = amount * 5;
  } else if (unit == '大さじ') {
    unit = 'ml';
    amount = amount * 15;
  }

  console.log('unit: ', unit, 'food_unit', food.food_unit);
  if (food.food_unit == 'g') {
    if (unit == 'g') {
      console.log('amount: ', amount, 'food_amount', food.food_amount);
      rate = amount / food.food_amount;
    } else if (unit == 'ml') {
      rate = amount / food.ml_per_food_unit;
    } else {
      rate = (amount * food.amount_per_package) / food.food_amount;
    }
  } else if (food.food_unit == 'ml') {
    if (unit == 'g') {
      rate = amount / food.gram_per_food_unit;
    } else if (unit == 'ml') {
      rate = amount / food.food_amount;
    } else {
      rate = (amount * food.amount_per_package) / food.food_amount;
    }
  } else if (food.food_unit == '単位') {
    if (unit == 'g') {
      rate = amount / food.gram_per_food_unit;
    } else if (unit == 'ml') {
      rate = amount / food.ml_per_food_unit;
    } else {
      rate = (amount * food.amount_per_package) / food.food_amount;
    }
  }
  console.log("rate: ", rate);

  return {
    food_id: food.id,
    name: food.name,
    amount: +input.amount,
    unit: input.unit,
    calory: food.calory * rate,
    protein: food.protein * rate,
    fat: food.fat * rate,
    carbs: food.carbs * rate,
    salt: food.salt * rate,
  };
}
