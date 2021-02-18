// Compiler for PHP (aka KPHP)
// Copyright (c) 2020 LLC «V Kontakte»
// Distributed under the GPL v3 License, see LICENSE.notice.txt

#include <cassert>
#include <cerrno>
#include <cstring>
#include <fcntl.h>
#include <unistd.h>

#include "common/macos-ports.h"
#include "server/task-workers/task-workers-context.h"

DEFINE_VERBOSITY(task_workers);

namespace task_workers {

void TaskWorkersContext::master_init_pipes(int task_result_slots_num) {
  if (pipes_inited) {
    return;
  }

  int err = pipe2(task_pipe.data(), O_NONBLOCK);
  if (err) {
    kprintf("Unable to create task pipe: %s", strerror(errno));
    assert(false);
    return;
  }

  result_pipes.resize(task_result_slots_num);
  for (int i = 0; i < result_pipes.size(); ++i) {
    auto &result_pipe = result_pipes.at(i);
    err = pipe2(result_pipe.data(), O_NONBLOCK);
    if (err) {
      kprintf("Unable to create result pipe: %s", strerror(errno));
      assert(false);
      return;
    }
  }

  pipes_inited = true;
}

} // namespace task_workers
