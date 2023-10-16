# Buto-Plugin-ValidatePid
Validate swedish PID (Personal Identification Number).
Last digit must match curren scoop.
Correct format is YYYYMMDD-NNNN (or YYYYMMDDNNNN) where last number is a check value 0-9.
```
19950622-8577
```

## Organisation number
One could also validate as organisation number.
Also suitable as personal number in some cases.
```
950622-8577
```

## Usage
### Form validator
Form validation.
```
items:
  pid:
    type: varchar
    label: 'PID'
    default: rs:pid
    validator:
      -
        plugin: 'validate/pid'
        method: validate_pid
        data:
```
Set data/delimitator to true. Pid must be in format 199506228577.
Only suitable if parameter organisation is NOT true.
```
          skip_delimitator: true
```
Set param organisation to validate as NNNNNN-NNNN (eleven digits).
Also used as personal pid in some cases.
```
          organisation: true
```

### Validate in code
```
wfPlugin::includeonce('validate/pid');
$obj = new PluginValidatePid();
$form = new PluginWfArray();
$form->set('items/pid/label', 'pid');
$form->set('items/pid/post_value', '19950622-8577');
$form->set('items/pid/is_valid', true);
wfHelp::yml_dump($obj->validate_pid('pid', $form->get()));
```

### Method isPid
Use this method to make use of all return values.
```
wfPlugin::includeonce('validate/pid');
$validate_pid = new PluginValidatePid();
$rs = $validate_pid->isPid('19950622-8577');
wfHelp::print($rs);
```
Returns.
```
pid_original: 19950622-8577
coordination_number: false
born: '1995-06-22'
sex: Male
pid: '9506228577'
pos:
  -
    value: '9'
    mult: 2
    value_mult: 18
    prod: 9
  -
    value: '5'
    mult: 1
    value_mult: 5
    prod: 5
  -
    value: '0'
    mult: 2
    value_mult: 0
    prod: 0
  -
    value: '6'
    mult: 1
    value_mult: 6
    prod: 6
  -
    value: '2'
    mult: 2
    value_mult: 4
    prod: 4
  -
    value: '2'
    mult: 1
    value_mult: 2
    prod: 2
  -
    value: '8'
    mult: 2
    value_mult: 16
    prod: 7
  -
    value: '5'
    mult: 1
    value_mult: 5
    prod: 5
  -
    value: '7'
    mult: 2
    value_mult: 14
    prod: 5
prod: 43
modulus: 7
check: '7'
ok: true
message: '?label has a correct control digit!'
```




## Source
- https://sv.wikipedia.org/wiki/Personnummer_i_Sverige